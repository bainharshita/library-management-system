<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$sql = "SELECT
            t.id,
            b.title,
            m.member_name,
            t.issue_date,
            t.due_date,
            t.return_date,
            t.fine,
            t.status
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        JOIN members m ON t.member_id = m.id
        ORDER BY t.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaction History | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title page-title-row">

        <div>

            <h1>Transaction History</h1>

            <p>View all book borrowing and return records.</p>

        </div>

        <a
            href="issue.php"
            class="btn btn-primary"
        >
            + Issue Book
        </a>

    </div>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Fine</th>
                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

            <?php while ($transaction = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo $transaction["id"]; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($transaction["member_name"]); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($transaction["title"]); ?>
                    </td>

                    <td>
                        <?php echo $transaction["issue_date"]; ?>
                    </td>

                    <td>
                        <?php echo $transaction["due_date"]; ?>
                    </td>

                    <td>

                        <?php

                        echo $transaction["return_date"]
                            ? $transaction["return_date"]
                            : "Not Returned";

                        ?>

                    </td>

                    <td>

                        ₹<?php echo $transaction["fine"]; ?>

                    </td>

                    <td>

                        <?php if ($transaction["status"] === "Issued"): ?>

                            <span class="status status-issued">
                                Issued
                            </span>

                        <?php else: ?>

                            <span class="status status-returned">
                                Returned
                            </span>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</main>

</body>

</html>