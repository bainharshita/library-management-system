
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

    <link rel="stylesheet" href="../assets/css/style.css?v=2">

    
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f7f2;
            color: #263b2d;
        }

        .student-dashboard {
            max-width: 1250px;
            margin: auto;
            padding: 40px 30px;
        }

        /* Header */
        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 35px;
        }

        .student-header h1 {
            color: #245b3c;
            font-size: 36px;
            letter-spacing: -0.8px;
            margin: 0 0 8px;
        }

        .student-header p {
            color: #718074;
            margin: 0;
            font-size: 16px;
        }

        .logout-button {
            background: #245b3c;
            color: white;
            padding: 13px 23px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.25s ease, transform 0.25s ease;
        }

        .logout-button:hover {
            background: #19472d;
            transform: translateY(-2px);
        }

        /* Welcome Banner */
        .welcome-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(120deg, #1f5b3b, #4f9165);
            color: white;
            padding: 42px;
            border-radius: 22px;
            margin-bottom: 35px;
            box-shadow: 0 12px 30px rgba(36, 91, 60, 0.12);
        }

        .welcome-card::after {
            content: "📚";
            position: absolute;
            right: 55px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 85px;
            opacity: 0.16;
            pointer-events: none;
        }

        .welcome-card h2 {
            font-size: clamp(24px, 3vw, 32px);
            margin: 0 0 12px;
            line-height: 1.3;
            position: relative;
            z-index: 1;
        }

        .welcome-card p {
            line-height: 1.8;
            font-size: 16px;
            max-width: 780px;
            margin: 0;
            color: rgba(255, 255, 255, 0.92);
            position: relative;
            z-index: 1;
        }

        /* Dashboard Cards */
        .student-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
        }

        .student-card {
            position: relative;
            background: white;
            padding: 30px 26px;
            border: 1px solid #e6eee5;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(38, 59, 45, 0.04);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-height: 260px;
            transition: transform 0.25s ease, box-shadow 0.25s ease,
                        border-color 0.25s ease;
        }

        .student-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 26px;
            width: 42px;
            height: 4px;
            background: #70aa76;
            border-radius: 0 0 5px 5px;
        }

        .student-card .icon {
            font-size: 36px;
            margin: 8px 0 14px;
        }

        .student-card h3 {
            color: #245b3c;
            font-size: 20px;
            margin: 0 0 10px;
            line-height: 1.4;
        }

        .student-card p {
            color: #718074;
            line-height: 1.75;
            font-size: 15px;
            margin: 0 0 18px;
        }

        .student-card a {
            display: inline-block;
            margin-top: auto;
            padding-top: 8px;
            color: #34734a;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .student-card a:hover {
            color: #19472d;
        }

        @media (hover: hover) {
            .student-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 14px 30px rgba(36, 91, 60, 0.10);
                border-color: #c9dfc9;
            }
        }

        /* Tablet */
        @media (max-width: 1000px) {
            .student-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .welcome-card::after {
                right: 30px;
                font-size: 70px;
            }
        }

        /* Mobile */
        @media (max-width: 600px) {
            .student-dashboard {
                padding: 24px 18px;
            }

            .student-header {
                margin-bottom: 25px;
            }

            .student-header h1 {
                font-size: 29px;
            }

            .logout-button {
                padding: 11px 17px;
            }

            .welcome-card {
                padding: 28px 24px;
                border-radius: 17px;
                margin-bottom: 25px;
            }

            .welcome-card::after {
                right: 15px;
                top: 15px;
                transform: none;
                font-size: 48px;
            }

            .welcome-card h2 {
                font-size: 24px;
                max-width: 90%;
            }

            .welcome-card p {
                font-size: 14px;
                line-height: 1.8;
            }

            .student-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .student-card {
                min-height: auto;
                padding: 26px 24px;
            }
        }

        /* Small phones */
        @media (max-width: 360px) {
            .student-dashboard {
                padding: 20px 14px;
            }

            .welcome-card {
                padding: 24px 18px;
            }

            .student-card {
                padding: 24px 20px;
            }
        }

        /* Reduced motion accessibility */
        @media (prefers-reduced-motion: reduce) {
            .student-card,
            .student-card a,
            .logout-button {
                transition: none;
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