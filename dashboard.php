
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

// Dashboard statistics
$queries = [
    "total_books" => "SELECT COUNT(*) AS total FROM books",
    "total_members" => "SELECT COUNT(*) AS total FROM members",
    "issued_books" => "SELECT COUNT(*) AS total FROM transactions WHERE status = 'Issued'",
    "overdue_books" => "SELECT COUNT(*) AS total FROM transactions WHERE status = 'Issued' AND due_date < CURDATE()",
    "pending_requests" => "SELECT COUNT(*) AS total FROM book_requests WHERE status = 'Pending'"
];

$stats = [];

foreach ($queries as $key => $sql) {
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $stats[$key] = $data["total"];
}

//overdue books alert
$overdue_sql = "
    SELECT
        b.title,
        m.member_name,
        t.due_date,
        DATEDIFF(CURDATE(), t.due_date) AS days_overdue
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    JOIN members m ON t.member_id = m.id
    WHERE t.status = 'Issued'
      AND t.due_date < CURDATE()
    ORDER BY t.due_date ASC
    LIMIT 5
";

$overdue_result = mysqli_query($conn, $overdue_sql);

// Recent transactions
$recent_sql = "
    SELECT
        t.id,
        b.title,
        m.member_name,
        t.issue_date,
        t.due_date,
        t.return_date,
        t.status
    FROM transactions t
    JOIN books b ON t.book_id = b.id
    JOIN members m ON t.member_id = m.id
    ORDER BY t.id DESC
    LIMIT 5
";

$recent_result = mysqli_query($conn, $recent_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GreenShelf</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include __DIR__ . "/includes/sidebar.php"; ?>

<main>

    <div class="page-title">
        <h1>Dashboard</h1>
        <p>
            Welcome back,
            <?php echo htmlspecialchars($_SESSION["username"]); ?> 👋
        </p>
    </div>

    <!-- Dashboard Statistics -->
    <div class="dashboard-cards">

        <div class="card">
            <h3>Total Books</h3>
            <p><?php echo $stats["total_books"]; ?></p>
        </div>

        <div class="card">
            <h3>Total Members</h3>
            <p><?php echo $stats["total_members"]; ?></p>
        </div>

        <div class="card">
            <h3>Issued Books</h3>
            <p><?php echo $stats["issued_books"]; ?></p>
        </div>

        <div class="card">
            <h3>Overdue Books</h3>
            <p><?php echo $stats["overdue_books"]; ?></p>
        </div>

        <div class="card">
            <h3>Pending Requests</h3>
            <p><?php echo $stats["pending_requests"]; ?></p>
        </div>

    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">

        <div class="section-header">
            <div>
                <h2>Library Overview</h2>
                <p>Quick access to your library operations.</p>
            </div>
        </div>

        <div class="quick-actions">

            <a href="books/add.php" class="action-card">
                <span class="action-icon">📚</span>
                <div>
                    <h3>Add Book</h3>
                    <p>Add a new book to the library.</p>
                </div>
            </a>

            <a href="members/add.php" class="action-card">
                <span class="action-icon">👤</span>
                <div>
                    <h3>Add Member</h3>
                    <p>Register a new library member.</p>
                </div>
            </a>

            <a href="transactions/issue.php" class="action-card">
                <span class="action-icon">📤</span>
                <div>
                    <h3>Issue Book</h3>
                    <p>Issue a book to a member.</p>
                </div>
            </a>

            <a href="book-requests.php" class="action-card">
                <span class="action-icon">📋</span>
                <div>
                    <h3>Book Requests</h3>
                    <p>Review pending student requests.</p>
                </div>
            </a>

            <a href="transactions/history.php" class="action-card">
                <span class="action-icon">📖</span>
                <div>
                    <h3>Transaction History</h3>
                    <p>View borrowing records.</p>
                </div>
            </a>

        </div>

    </div>

    
<!-- Overdue Books Alert -->
<div class="dashboard-section">

    <div class="section-header">
        <div>
            <h2>⚠️ Overdue Books</h2>
            <p>Books that have passed their return date.</p>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Member</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                </tr>
            </thead>

            <tbody>

            <?php if (mysqli_num_rows($overdue_result) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($overdue_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                        <td><?php echo htmlspecialchars($row["member_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["due_date"]); ?></td>
                        <td>
                            <strong style="color: #dc2626;">
                                <?php echo (int)$row["days_overdue"]; ?> days
                            </strong>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4">🎉 No overdue books. Everything is up to date!</td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>

    </div>

    <!-- Recent Transactions -->
    <div class="dashboard-section">

        <div class="section-header">
            <div>
                <h2>Recent Transactions</h2>
                <p>Latest book issue and return records.</p>
            </div>
        </div>

        <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($recent_result) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($recent_result)): ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row["title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["member_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["issue_date"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row["due_date"]); ?>
                            </td>

                            <td>
                                <?php if ($row["status"] === "Issued"): ?>
                                    <span class="status-issued">Issued</span>
                                <?php else: ?>
                                    <span class="status-returned">Returned</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5">No transactions found.</td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

</main>

</body>
</html>