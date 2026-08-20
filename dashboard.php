<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

$result = mysqli_query($conn, "SELECT COUNT(*) AS total_books FROM books");
$data = mysqli_fetch_assoc($result);
$total_books = $data["total_books"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total_members FROM members");
$data = mysqli_fetch_assoc($result);
$total_members = $data["total_members"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS issued_books FROM transactions WHERE status = 'Issued'");
$data = mysqli_fetch_assoc($result);
$issued_books = $data["issued_books"];

$result = mysqli_query($conn, "SELECT COUNT(*) AS overdue_books FROM transactions WHERE status = 'Issued' AND due_date < CURDATE()");
$data = mysqli_fetch_assoc($result);
$overdue_books = $data["overdue_books"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Library Management</title>

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


    <div class="dashboard-cards">

        <div class="card">

            <h3>Total Books</h3>

            <p>
                <?php echo $total_books; ?>
            </p>

        </div>


        <div class="card">

            <h3>Total Members</h3>

            <p>
                <?php echo $total_members; ?>
            </p>

        </div>


        <div class="card">

            <h3>Issued Books</h3>

            <p>
                <?php echo $issued_books; ?>
            </p>

        </div>


        <div class="card">

            <h3>Overdue Books</h3>

            <p>
                <?php echo $overdue_books; ?>
            </p>

        </div>

    </div>


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


            <a href="transactions/history.php" class="action-card">

                <span class="action-icon">📋</span>

                <div>
                    <h3>Transaction History</h3>
                    <p>View borrowing records.</p>
                </div>

            </a>

        </div>

    </div>

</main>

</body>

</html>