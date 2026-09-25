
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
            t.id,
            b.title,
            b.author,
            b.isbn,
            t.issue_date,
            t.due_date,
            t.fine,
            t.status
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        WHERE t.member_id = ?
          AND t.status = 'Issued'
        ORDER BY t.due_date ASC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $studentId);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$today = date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Borrowed Books | GreenShelf</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .borrowed-page {
            padding: 30px;
        }

        .borrowed-heading {
            margin-bottom: 25px;
        }

        .borrowed-heading h1 {
            color: #245b3c;
            margin-bottom: 8px;
        }

        .borrowed-heading p {
            color: #718074;
        }

        .borrowed-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #34734a;
            text-decoration: none;
            font-weight: 600;
        }

        .borrowed-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 22px;
        }

        .borrowed-card {
            background: white;
            border: 1px solid #e7eee6;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }

        .borrowed-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .borrowed-card h3 {
            color: #245b3c;
            margin: 0 0 10px;
        }

        .borrowed-author {
            color: #718074;
            margin-bottom: 20px;
        }

        .borrowed-details {
            border-top: 1px solid #edf1eb;
            padding-top: 15px;
        }

        .borrowed-details p {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 12px 0;
            font-size: 14px;
        }

        .borrowed-details strong {
            color: #344b38;
            text-align: right;
        }

        .loan-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #e4f5e7;
            color: #28743b;
        }

        .status-overdue {
            background: #fde8e7;
            color: #a33b35;
        }

        .borrowed-empty {
            background: white;
            padding: 45px 20px;
            text-align: center;
            border-radius: 14px;
            color: #718074;
        }

        .borrowed-empty .icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            .borrowed-page {
                padding: 20px;
            }

            .borrowed-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>

<body>

<main class="borrowed-page">

    <a href="dashboard.php" class="borrowed-back">
        ← Back to Dashboard
    </a>

    <div class="borrowed-heading">
        <h1>📚 My Borrowed Books</h1>
        <p>Keep track of your currently borrowed books and due dates.</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="borrowed-grid">

            <?php while ($book = mysqli_fetch_assoc($result)): ?>

                <?php
                    $dueDate = $book["due_date"];
                    $isOverdue = $dueDate < $today;
                ?>

                <article class="borrowed-card">

                    <div class="borrowed-icon">📖</div>

                    <h3>
                        <?php echo htmlspecialchars($book["title"]); ?>
                    </h3>

                    <p class="borrowed-author">
                        By <?php echo htmlspecialchars($book["author"]); ?>
                    </p>

                    <div class="borrowed-details">

                        <p>
                            <span>ISBN</span>
                            <strong>
                                <?php echo htmlspecialchars($book["isbn"]); ?>
                            </strong>
                        </p>

                        <p>
                            <span>Issue Date</span>
                            <strong>
                                <?php echo htmlspecialchars($book["issue_date"]); ?>
                            </strong>
                        </p>

                        <p>
                            <span>Due Date</span>
                            <strong>
                                <?php echo htmlspecialchars($dueDate); ?>
                            </strong>
                        </p>

                        <p>
                            <span>Fine</span>
                            <strong>
                                ₹<?php echo number_format((float) $book["fine"], 2); ?>
                            </strong>
                        </p>

                        <p>
                            <span>Status</span>

                            <?php if ($isOverdue): ?>

                                <span class="loan-status status-overdue">
                                    Overdue
                                </span>

                            <?php else: ?>

                                <span class="loan-status status-active">
                                    Issued
                                </span>

                            <?php endif; ?>
                        </p>

                    </div>

                </article>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="borrowed-empty">

            <div class="icon">📚</div>

            <h2>No borrowed books yet</h2>

            <p>
                You don't have any books currently issued to you.
                Browse the library and discover your next read!
            </p>

            <a href="books.php" class="btn btn-primary">
                Browse Books
            </a>

        </div>

    <?php endif; ?>

</main>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>