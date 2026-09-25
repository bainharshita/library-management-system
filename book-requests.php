
<?php

session_start();

if (
    !isset($_SESSION["admin_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

$message = "";
$messageType = "";

/* Handle approve or reject actions */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $requestId = (int) ($_POST["request_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($requestId <= 0) {

        $message = "Invalid request.";
        $messageType = "error";

    } elseif (!in_array($action, ["approve", "reject"], true)) {

        $message = "Invalid action.";
        $messageType = "error";

    } else {

        mysqli_begin_transaction($conn);

        try {

            // Lock the request row to prevent simultaneous processing.
            $sql = "SELECT id, member_id, book_id, status
                    FROM book_requests
                    WHERE id = ?
                    FOR UPDATE";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $requestId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $request = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$request) {

                throw new Exception("Request not found.");

            } elseif ($request["status"] !== "Pending") {

                throw new Exception("This request has already been processed.");

            }

            if ($action === "reject") {

                $sql = "UPDATE book_requests
                        SET status = 'Rejected'
                        WHERE id = ? AND status = 'Pending'";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $requestId);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) !== 1) {
                    throw new Exception("Unable to reject this request.");
                }

                mysqli_stmt_close($stmt);

                mysqli_commit($conn);

                $message = "Book request rejected successfully.";
                $messageType = "success";

            } else {

                $dueDate = $_POST["due_date"] ?? "";
                $issueDate = date("Y-m-d");

                $dateObject = DateTime::createFromFormat("!Y-m-d", $dueDate);

                if (
                    !$dateObject ||
                    $dateObject->format("Y-m-d") !== $dueDate ||
                    $dueDate < $issueDate
                ) {
                    throw new Exception("Please select a valid due date.");
                }

                // Lock the book row and verify availability.
                $sql = "SELECT available
                        FROM books
                        WHERE id = ?
                        FOR UPDATE";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $request["book_id"]);
                mysqli_stmt_execute($stmt);

                $bookResult = mysqli_stmt_get_result($stmt);
                $book = mysqli_fetch_assoc($bookResult);
                mysqli_stmt_close($stmt);

                if (!$book || (int) $book["available"] <= 0) {
                    throw new Exception("No copies of this book are currently available.");
                }

                // Prevent duplicate active borrowing of the same book.
                $sql = "SELECT id
                        FROM transactions
                        WHERE member_id = ?
                          AND book_id = ?
                          AND status = 'Issued'
                        LIMIT 1";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "ii",
                    $request["member_id"],
                    $request["book_id"]
                );

                mysqli_stmt_execute($stmt);

                $issuedResult = mysqli_stmt_get_result($stmt);
                $alreadyIssued = mysqli_num_rows($issuedResult) > 0;

                mysqli_stmt_close($stmt);

                if ($alreadyIssued) {
                    throw new Exception("This student already has this book issued.");
                }

                // Create the borrowing transaction.
                $sql = "INSERT INTO transactions
                        (book_id, member_id, issue_date, due_date, status)
                        VALUES (?, ?, ?, ?, 'Issued')";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "iiss",
                    $request["book_id"],
                    $request["member_id"],
                    $issueDate,
                    $dueDate
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Decrease the available quantity.
                $sql = "UPDATE books
                        SET available = available - 1
                        WHERE id = ? AND available > 0";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $request["book_id"]);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) !== 1) {
                    throw new Exception("Unable to update book availability.");
                }

                mysqli_stmt_close($stmt);

                // Mark the request as approved.
                $sql = "UPDATE book_requests
                        SET status = 'Approved'
                        WHERE id = ? AND status = 'Pending'";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $requestId);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) !== 1) {
                    throw new Exception("Unable to approve this request.");
                }

                mysqli_stmt_close($stmt);

                mysqli_commit($conn);

                $message = "Request approved! Book issued successfully.";
                $messageType = "success";
            }

        } catch (Throwable $e) {

            mysqli_rollback($conn);

            $message = $e->getMessage();
            $messageType = "error";
        }
    }
}

/* Fetch all requests */

$sql = "SELECT
            r.id,
            r.request_date,
            r.status,
            m.member_name,
            m.email,
            b.title,
            b.author,
            b.available
        FROM book_requests r
        JOIN members m ON r.member_id = m.id
        JOIN books b ON r.book_id = b.id
        ORDER BY
            CASE WHEN r.status = 'Pending' THEN 0 ELSE 1 END,
            r.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Requests | GreenShelf</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .requests-page {
            padding: 30px;
        }

        .requests-heading {
            margin-bottom: 25px;
        }

        .requests-heading h1 {
            color: #245b3c;
            margin-bottom: 8px;
        }

        .requests-heading p {
            color: #718074;
        }

        .requests-message {
            padding: 14px 18px;
            border-radius: 9px;
            margin-bottom: 22px;
        }

        .requests-message.success {
            background: #e4f5e7;
            color: #28743b;
        }

        .requests-message.error {
            background: #fde8e7;
            color: #a33b35;
        }

        .requests-table-container {
            overflow-x: auto;
            background: white;
            border: 1px solid #e7eee6;
            border-radius: 12px;
        }

        .requests-table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
        }

        .requests-table th,
        .requests-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #edf1eb;
        }

        .requests-table th {
            background: #edf5eb;
            color: #245b3c;
        }

        .request-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff1d6;
            color: #8b5d12;
        }

        .status-approved {
            background: #e4f5e7;
            color: #28743b;
        }

        .status-rejected {
            background: #fde8e7;
            color: #a33b35;
        }

        .request-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 180px;
        }

        .request-actions input[type="date"] {
            padding: 8px;
            border: 1px solid #dce5dc;
            border-radius: 6px;
            font: inherit;
        }

        .request-actions button {
            padding: 9px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .approve-btn {
            background: #245b3c;
            color: white;
        }

        .reject-btn {
            background: #fde8e7;
            color: #a33b35;
        }

        .request-empty {
            background: white;
            padding: 45px 20px;
            text-align: center;
            color: #718074;
            border-radius: 12px;
        }

        @media (max-width: 600px) {
            .requests-page {
                padding: 20px;
            }
        }
    </style>

</head>

<body>

<?php include __DIR__ . "/includes/sidebar.php"; ?>

<main class="requests-page">

    <div class="requests-heading">
        <h1>📋 Book Requests</h1>
        <p>Review student requests and manage book approvals.</p>
    </div>

    <?php if ($message !== ""): ?>
        <div class="requests-message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>

        <div class="requests-table-container">

            <table class="requests-table">

                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Request Date</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($request = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($request["member_name"]); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["author"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["request_date"]); ?>
                            </td>

                            <td>
                                <?php echo (int) $request["available"]; ?>
                            </td>

                            <td>
                                <?php
                                    $statusClass = "status-pending";

                                    if ($request["status"] === "Approved") {
                                        $statusClass = "status-approved";
                                    } elseif ($request["status"] === "Rejected") {
                                        $statusClass = "status-rejected";
                                    }
                                ?>

                                <span class="request-status <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($request["status"]); ?>
                                </span>
                            </td>

                            <td>

                                <?php if ($request["status"] === "Pending"): ?>

                                    <div class="request-actions">

                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="request_id"
                                                value="<?php echo (int) $request["id"]; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="approve"
                                            >

                                            <label>
                                                Due date
                                                <input
                                                    type="date"
                                                    name="due_date"
                                                    min="<?php echo date('Y-m-d'); ?>"
                                                    value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>"
                                                    required
                                                >
                                            </label>

                                            <button
                                                type="submit"
                                                class="approve-btn"
                                                <?php echo (int) $request["available"] <= 0 ? "disabled" : ""; ?>
                                            >
                                                ✓ Approve & Issue
                                            </button>

                                        </form>

                                        <form method="POST"
                                              onsubmit="return confirm('Reject this book request?');">

                                            <input
                                                type="hidden"
                                                name="request_id"
                                                value="<?php echo (int) $request["id"]; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="reject"
                                            >

                                            <button type="submit" class="reject-btn">
                                                ✕ Reject Request
                                            </button>

                                        </form>

                                    </div>

                                <?php else: ?>

                                    <span>Processed</span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="request-empty">
            <h2>📚 No book requests yet</h2>
            <p>Student requests will appear here.</p>
        </div>

    <?php endif; ?>

</main>

</body>
</html>