<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$message = "";


/* Return Book */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $transaction_id = (int)($_POST["transaction_id"] ?? 0);

    if ($transaction_id <= 0) {

        $message = "Invalid transaction.";

    } else {

        $sql = "SELECT book_id, due_date, status
                FROM transactions
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $transaction_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $transaction = mysqli_fetch_assoc($result);


        if (!$transaction) {

            $message = "Transaction not found.";

        } elseif ($transaction["status"] != "Issued") {

            $message = "This book has already been returned.";

        } else {

            $return_date = date("Y-m-d");

            $due_date = new DateTime($transaction["due_date"]);
            $return = new DateTime($return_date);

            $fine = 0;

            if ($return > $due_date) {

                $days_late = $due_date->diff($return)->days;

                $fine_per_day = 5;

                $fine = $days_late * $fine_per_day;
            }


            mysqli_begin_transaction($conn);

            try {

                /* Update transaction */

                $sql = "UPDATE transactions
                        SET return_date = ?,
                            fine = ?,
                            status = 'Returned'
                        WHERE id = ?";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "sdi",
                    $return_date,
                    $fine,
                    $transaction_id
                );

                mysqli_stmt_execute($stmt);


                /* Increase available books */

                $sql = "UPDATE books
                        SET available = available + 1
                        WHERE id = ?";

                $stmt = mysqli_prepare($conn, $sql);

                mysqli_stmt_bind_param(
                    $stmt,
                    "i",
                    $transaction["book_id"]
                );

                mysqli_stmt_execute($stmt);


                mysqli_commit($conn);

                $message = "Book returned successfully! Fine: ₹" . $fine;

            } catch (Exception $e) {

                mysqli_rollback($conn);

                $message = "Unable to return book.";
            }
        }
    }
}


/* Get currently issued books */

$sql = "SELECT
            t.id,
            t.issue_date,
            t.due_date,
            b.title,
            m.member_name
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        JOIN members m ON t.member_id = m.id
        WHERE t.status = 'Issued'
        ORDER BY t.due_date ASC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Return Book</title>

</head>

<body>

<h1>Return Book</h1>


<?php if ($message): ?>

    <p>
        <?php echo htmlspecialchars($message); ?>
    </p>

<?php endif; ?>


<table border="1" cellpadding="10">

    <thead>

        <tr>

            <th>Member</th>
            <th>Book</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Action</th>

        </tr>

    </thead>


    <tbody>

    <?php while ($transaction = mysqli_fetch_assoc($result)): ?>

        <tr>

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

                <form method="POST">

                    <input
                        type="hidden"
                        name="transaction_id"
                        value="<?php echo $transaction["id"]; ?>"
                    >

                    <button type="submit">
                        Return
                    </button>

                </form>

            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>


<br>

<a href="../dashboard.php">
    Back to Dashboard
</a>

</body>

</html>