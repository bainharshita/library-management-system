
<?php

session_start();

if (
    !isset($_SESSION["student_id"]) ||
    ($_SESSION["role"] ?? "") !== "student"
) {
    header("Location: ../login.php");
    exit();
}

$memberName = $_SESSION["member_name"] ?? "Student";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GreenShelf | Student Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f7f2;
            color: #263b2d;
        }

        .student-dashboard {
            max-width: 1150px;
            margin: auto;
            padding: 35px 25px;
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .student-header h1 {
            color: #245b3c;
            margin: 0 0 8px;
        }

        .student-header p {
            color: #718074;
            margin: 0;
        }

        .logout-button {
            background: #245b3c;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
        }

        .welcome-card {
            background: linear-gradient(135deg, #245b3c, #57956b);
            color: white;
            padding: 35px;
            border-radius: 18px;
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            font-size: 28px;
            margin-top: 0;
        }

        .welcome-card p {
            line-height: 1.7;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
        }

        .student-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .student-card .icon {
            font-size: 32px;
        }

        .student-card h3 {
            color: #245b3c;
        }

        .student-card p {
            color: #718074;
            line-height: 1.6;
        }

        .student-card a {
            display: inline-block;
            margin-top: 10px;
            color: #34734a;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .student-dashboard {
                padding: 20px;
            }

            .welcome-card {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <main class="student-dashboard">

        <header class="student-header">
            <div>
                <h1>🌿 GreenShelf</h1>
                <p>Student Library Portal</p>
            </div>

            <a href="../logout.php" class="logout-button">
                Log Out
            </a>
        </header>

        <section class="welcome-card">
            <h2>
                Welcome,
                <?php echo htmlspecialchars($memberName); ?>! 📚
            </h2>

            <p>
                Welcome to your personal library space.
                Explore books, keep track of your borrowed books,
                and manage your library account.
            </p>
        </section>

        <section class="student-grid">

            <article class="student-card">
                <div class="icon">📖</div>
                <h3>Browse Books</h3>
                <p>Explore the library collection and discover your next read.</p>
                <a href="books.php">Explore Books →</a>
            </article>

            <article class="student-card">
                <div class="icon">📚</div>
                <h3>My Borrowed Books</h3>
                <p>View your borrowed books and check their return dates.</p>
                <a href="my-books.php">View My Books →</a>
            </article>

            <article class="student-card">
                <div class="icon">💰</div>
                <h3>My Fines</h3>
                <p>Check any outstanding fines associated with your account.</p>
                <a href="my-fines.php">View My Fines →</a>
            </article>

            <article class="student-card">
                <div class="icon">👤</div>
                <h3>My Profile</h3>
                <p>View your personal information registered with GreenShelf.</p>
                <a href="profile.php">View Profile →</a>
            </article>

        </section>

    </main>

</body>
</html>