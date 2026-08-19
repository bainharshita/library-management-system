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
<html>
<head>
    <title>Dashboard - Library Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Library Management</title>
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

</main>

</body>
</html>