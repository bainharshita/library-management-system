
<?php

session_start();

if (
    !isset($_SESSION["student_id"]) ||
    ($_SESSION["role"] ?? "") !== "student"
) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$studentId = (int) $_SESSION["student_id"];

$sql = "SELECT
            r.id,
            r.request_date,
            r.status,
            b.title,
            b.author,
            b.isbn
        FROM book_requests r
        JOIN books b ON r.book_id = b.id
        WHERE r.member_id = ?
        ORDER BY r.id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$requests = [];

while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Requests | GreenShelf</title>

    <link rel="stylesheet" href="../assets/css/style.css">

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

        .requests-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #34734a;
            text-decoration: none;
            font-weight: 600;
        }

        .requests-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: white;
            border: 1px solid #e7eee6;
            border-radius: 12px;
            padding: 20px;
        }

        .summary-card p {
            color: #718074;
            margin: 0 0 10px;
        }

        .summary-card h2 {
            color: #245b3c;
            margin: 0;
            font-size: 28px;
        }

        .requests-table-container {
            overflow-x: auto;
            background: white;
            border: 1px solid #e7eee6;
            border-radius: 12px;
        }

        .requests-table {
            width: 100%;
            min-width: 750px;
            border-collapse: collapse;
        }

        .requests-table th,
        .requests-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #edf1eb;
        }

        .requests-table th {
            background: #edf5eb;
            color: #245b3c;
        }

        .request-status {
            display: inline-block;
            padding: 7px 12px;
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

        .requests-empty {
            background: white;
            padding: 45px 20px;
            text-align: center;
            border-radius: 14px;
            color: #718074;
        }

        .requests-empty a {
            display: inline-block;
            margin-top: 15px;
            color: #34734a;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .requests-page {
                padding: 20px;
            }
        }
    </style>

</head>

<body>

<main class="requests-page">

    <a href="dashboard.php" class="requests-back">
        ← Back to Dashboard
    </a>

    <div class="requests-heading">
        <h1>📋 My Requests</h1>
        <p>Track the status of your book requests.</p>
    </div>

    <?php
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($requests as $request) {
            if ($request["status"] === "Pending") {
                $pendingCount++;
            } elseif ($request["status"] === "Approved") {
                $approvedCount++;
            } elseif ($request["status"] === "Rejected") {
                $rejectedCount++;
            }
        }
    ?>

    <section class="requests-summary">

        <div class="summary-card">
            <p>Total Requests</p>
            <h2><?php echo count($requests); ?></h2>
        </div>

        <div class="summary-card">
            <p>Pending</p>
            <h2><?php echo $pendingCount; ?></h2>
        </div>

        <div class="summary-card">
            <p>Approved</p>
            <h2><?php echo $approvedCount; ?></h2>
        </div>

        <div class="summary-card">
            <p>Rejected</p>
            <h2><?php echo $rejectedCount; ?></h2>
        </div>

    </section>

    <?php if (count($requests) > 0): ?>

        <div class="requests-table-container">

            <table class="requests-table">

                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($requests as $request): ?>

                        <?php
                            $statusClass = "status-pending";

                            if ($request["status"] === "Approved") {
                                $statusClass = "status-approved";
                            } elseif ($request["status"] === "Rejected") {
                                $statusClass = "status-rejected";
                            }
                        ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($request["title"]); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["author"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["isbn"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($request["request_date"]); ?>
                            </td>

                            <td>
                                <span class="request-status <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($request["status"]); ?>
                                </span>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="requests-empty">
            <h2>📚 No requests yet</h2>
            <p>You haven't requested any books. Explore the library to get started.</p>

            <a href="books.php">Browse Books →</a>
        </div>

    <?php endif; ?>

</main>

</body>
</html>