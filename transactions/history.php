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
<html>

<head>
    <title>Transaction History</title>
</head>

<body>

<h1>Transaction History</h1>

<table border="1" cellpadding="10">

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
                <?php echo htmlspecialchars($transaction["member_name"]); ?>
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
                <?php echo htmlspecialchars($transaction["status"]); ?>
            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>

<br>

<a href="issue.php">Issue Book</a> |
<a href="return.php">Return Book</a> |
<a href="../dashboard.php">Dashboard</a>

</body>

</html>