<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$message = "";

$books_sql = "SELECT id, title, available
              FROM books
              WHERE available > 0
              ORDER BY title";

$books_result = mysqli_query($conn, $books_sql);

$members_sql = "SELECT id, member_name
                FROM members
                ORDER BY member_name";

$members_result = mysqli_query($conn, $members_sql);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $book_id = (int)($_POST["book_id"] ?? 0);
    $member_id = (int)($_POST["member_id"] ?? 0);
    $issue_date = $_POST["issue_date"] ?? "";
    $due_date = $_POST["due_date"] ?? "";

    if (
        $book_id <= 0 ||
        $member_id <= 0 ||
        $issue_date == "" ||
        $due_date == ""
    ) {

        $message = "Please fill in all fields.";

    } elseif ($due_date < $issue_date) {

        $message = "Due date cannot be before issue date.";

    } else {

        $sql = "SELECT available
                FROM books
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);

        if (!$book || $book["available"] <= 0) {

            $message = "This book is not available.";

        } else {

            mysqli_begin_transaction($conn);

            try {

                $sql = "INSERT INTO transactions
                        (book_id, member_id, issue_date, due_date, status)
                        VALUES (?, ?, ?, ?, 'Issued')";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "iiss",
                    $book_id,
                    $member_id,
                    $issue_date,
                    $due_date
                );

                mysqli_stmt_execute($stmt);


                $sql = "UPDATE books
                        SET available = available - 1
                        WHERE id = ? AND available > 0";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param($stmt, "i", $book_id);

                mysqli_stmt_execute($stmt);

                mysqli_commit($conn);

                $message = "Book issued successfully!";

            } catch (Exception $e) {

                mysqli_rollback($conn);

                $message = "Unable to issue book.";

            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Issue Book</title>
</head>

<body>

<h1>Issue Book</h1>

<?php if ($message): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>


<form method="POST">

    <label>Member</label><br>

    <select name="member_id" required>

        <option value="">Select Member</option>

        <?php while ($member = mysqli_fetch_assoc($members_result)): ?>

            <option value="<?php echo $member["id"]; ?>">
                <?php echo htmlspecialchars($member["member_name"]); ?>
            </option>

        <?php endwhile; ?>

    </select>


    <br><br>


    <label>Book</label><br>

    <select name="book_id" required>

        <option value="">Select Book</option>

        <?php while ($book = mysqli_fetch_assoc($books_result)): ?>

            <option value="<?php echo $book["id"]; ?>">

                <?php echo htmlspecialchars($book["title"]); ?>

                (<?php echo $book["available"]; ?> available)

            </option>

        <?php endwhile; ?>

    </select>


    <br><br>


    <label>Issue Date</label><br>

    <input
        type="date"
        name="issue_date"
        value="<?php echo date('Y-m-d'); ?>"
        required
    >


    <br><br>


    <label>Due Date</label><br>

    <input
        type="date"
        name="due_date"
        required
    >


    <br><br>


    <button type="submit">
        Issue Book
    </button>

</form>

<br>

<a href="../dashboard.php">Back to Dashboard</a>

</body>

</html>