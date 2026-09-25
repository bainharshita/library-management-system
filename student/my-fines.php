
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
            t.issue_date,
            t.due_date,
            t.return_date,
            t.fine,
            t.status
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        WHERE t.member_id = ?
          AND t.fine > 0
        ORDER BY t.id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $studentId);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$totalFine = 0;

$fines = [];

while ($fine = mysqli_fetch_assoc($result)) {
    $fines[] = $fine;
    $totalFine += (float) $fine["fine"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Fines | GreenShelf</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .fines-page {
            padding: 30px;
        }

        .fines-heading {
            margin-bottom: 25px;
        }

        .fines-heading h1 {
            color: #245b3c;
            margin-bottom: 8px;
        }

        .fines-heading p {
            color: #718074;
        }

        .fines-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #34734a;
            text-decoration: none;
            font-weight: 600;
        }

        .fine-summary {
            background: linear-gradient(135deg, #245b3c, #57956b);
            color: white;
            padding: 28px;
            border-radius: 16px;
            margin-bottom: 28px;
        }

        .fine-summary p {
            margin: 0 0 10px;
            opacity: 0.9;
        }

        .fine-summary h2 {
            font-size: 32px;
            margin: 0;
        }

        .fine-summary span {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.9;
        }

        .fines-table-container {
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            border: 1px solid #e7eee6;
        }

        .fines-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .fines-table th,
        .fines-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #edf1eb;
        }

        .fines-table th {
            background: #edf5eb;
            color: #245b3c;
        }

        .fine-amount {
            font-weight: bold;
            color: #a33b35;
        }

        .fine-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .fine-returned {
            background: #e4f5e7;
            color: #28743b;
        }

        .fine-issued {
            background: #fff1d6;
            color: #8b5d12;
        }

        .fine-empty {
            background: white;
            padding: 45px 20px;
            text-align: center;
            border-radius: 14px;
            color: #718074;
        }

        .fine-empty .icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        @media (max-width: 600px) {
            .fines-page {
                padding: 20px;
            }

            .fine-summary {
                padding: 22px;
            }
        }
    </style>

</head>

<body>

<main class="fines-page">

    <a href="dashboard.php" class="fines-back">
        ← Back to Dashboard
    </a>

    <div class="fines-heading">
        <h1>💰 My Fines</h1>
        <p>View the fines recorded for your borrowed books.</p>
    </div>

    <section class="fine-summary">

        <p>Total Recorded Fines</p>

        <h2>
            ₹<?php echo number_format($totalFine, 2); ?>
        </h2>

        <span>
            <?php echo count($fines); ?>
            fine record(s)
        </span>

    </section>

    <?php if (count($fines) > 0): ?>

        <div class="fines-table-container">

            <table class="fines-table">

                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Fine</th>
                        <th>Transaction Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($fines as $fine): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($fine["title"]); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fine["issue_date"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($fine["due_date"]); ?>
                            </td>

                            <td>
                                <?php
                                echo $fine["return_date"]
                                    ? htmlspecialchars($fine["return_date"])
                                    : "Not Returned";
                                ?>
                            </td>

                            <td class="fine-amount">
                                ₹<?php echo number_format((float) $fine["fine"], 2); ?>
                            </td>

                            <td>

                                <?php if ($fine["status"] === "Issued"): ?>

                                    <span class="fine-status fine-issued">
                                        Issued
                                    </span>

                                <?php else: ?>

                                    <span class="fine-status fine-returned">
                                        Returned
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="fine-empty">

            <div class="icon">🌿</div>

            <h2>No fine records</h2>

            <p>
                You currently have no fines recorded in your account.
                Keep enjoying your reading!
            </p>

        </div>

    <?php endif; ?>

</main>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>