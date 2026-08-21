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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Issue Book | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title">

        <h1>Issue Book</h1>

        <p>Issue a book to a registered library member.</p>

    </div>


    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="form-container">

        <form method="POST">


            <div class="form-group">

                <label for="member_id">
                    Select Member
                </label>

                <select
                    name="member_id"
                    id="member_id"
                    required
                >

                    <option value="">
                        Select a member
                    </option>

                    <?php while ($member = mysqli_fetch_assoc($members_result)): ?>

                        <option value="<?php echo $member["id"]; ?>">

                            <?php echo htmlspecialchars($member["member_name"]); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="book_id">
                    Select Book
                </label>

                <select
                    name="book_id"
                    id="book_id"
                    required
                >

                    <option value="">
                        Select an available book
                    </option>

                    <?php while ($book = mysqli_fetch_assoc($books_result)): ?>

                        <option value="<?php echo $book["id"]; ?>">

                            <?php echo htmlspecialchars($book["title"]); ?>

                            (<?php echo $book["available"]; ?> available)

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="form-row">

                <div class="form-group">

                    <label for="issue_date">
                        Issue Date
                    </label>

                    <input
                        type="date"
                        name="issue_date"
                        id="issue_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="due_date">
                        Due Date
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        id="due_date"
                        required
                    >

                </div>

            </div>


            <div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    📤 Issue Book
                </button>

                <a
                    href="../dashboard.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</main>

</body>

</html>