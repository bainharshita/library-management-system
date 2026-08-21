<?php

session_start();

require_once __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Please enter your username and password.";

    } else {

        $sql = "SELECT id, username, password
                FROM admins
                WHERE username = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $username);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $admin = mysqli_fetch_assoc($result);


        if ($admin) {

            if (password_verify($password, $admin["password"])) {

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["username"] = $admin["username"];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Incorrect username or password.";

            }

        } else {

            $error = "Incorrect username or password.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>GreenShelf | Library Management System</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body class="landing-page">


<!-- NAVBAR -->

<header class="landing-navbar">

    <div class="brand">

        <div class="brand-icon">
            📖
        </div>

        <div>

            <h2>Green<span>Shelf</span></h2>

            <p>Library Management System</p>

        </div>

    </div>


    <nav>

        <a href="#home">Home</a>

        <a href="#about">About Us</a>

        <a href="#features">Features</a>

        <a href="#pricing">Pricing</a>

        <a href="#contact">Contact Us</a>

        <a href="#login" class="nav-login">
            Sign In
        </a>

    </nav>

</header>


<!-- HERO -->

<section class="hero" id="home">


    <!-- LEFT CONTENT -->

    <div class="hero-content">

        <div class="hero-badge">
            Smart Library. Simplified.
        </div>


        <h1>

            Revolutionize Your Library

            <br>

            with the
            <span>Best Library</span>

            <br>

            Management Software

        </h1>


        <p class="hero-description">

            Effortlessly manage your library from book
            collections to member records, transactions
            and reporting, all in one place.

        </p>


        <div class="hero-line"></div>


        <p class="hero-subtext">

            Optimize your library operations with our
            feature-rich, user-friendly management system
            built for modern libraries.

        </p>


        <div class="hero-buttons">

            <a
                href="#login"
                class="hero-primary"
            >
                🌿 Get Started Now
            </a>


            <a
                href="#features"
                class="hero-demo"
            >

                <span class="play-button">
                    ▶
                </span>

                Watch Demo

            </a>

        </div>


        <!-- SIMPLE BOOK ILLUSTRATION -->

        <div class="book-illustration">

            <div class="book book-one">
                GreenShelf
            </div>

            <div class="book book-two">
                Library
            </div>

            <div class="book book-three">
                Knowledge
            </div>

            <div class="plant">

                <span>🌿</span>

            </div>

        </div>

    </div>



    <!-- LOGIN CARD -->

    <div class="login-wrapper" id="login">

        <div class="login-card">


            <div class="login-icon">
                📖
            </div>


            <h2>
                Welcome Back
            </h2>


            <p class="login-subtitle">

                Sign in to access your library dashboard

            </p>


            <?php if ($error !== ""): ?>

                <div class="login-error">

                    ⚠️
                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="login-field">

                    <label for="username">
                        Username
                    </label>

                    <div class="input-wrapper">

                        <span>👤</span>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="login-field">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <span>🔒</span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            👁
                        </button>

                    </div>

                </div>


                <div class="login-options">

                    <label>

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        Remember me

                    </label>


                    <a href="#">
                        Forgot Password?
                    </a>

                </div>


                <button
                    type="submit"
                    class="login-button"
                >

                    🔐 Sign In

                </button>


            </form>


            <div class="login-divider">

                <span></span>

                <p>or</p>

                <span></span>

            </div>


            <p class="admin-note">

                Don't have an account?

                <a href="signup.php">
                    Contact Admin
                </a>

            </p>


        </div>

    </div>

</section>



<!-- FEATURES -->

<section
    class="feature-strip"
    id="features"
>


    <div class="feature-item">

        <div class="feature-icon">
            🛡️
        </div>

        <div>

            <h3>
                Secure & Reliable
            </h3>

            <p>
                Your library data stays protected.
            </p>

        </div>

    </div>



    <div class="feature-item">

        <div class="feature-icon">
            ⏱️
        </div>

        <div>

            <h3>
                Save Time
            </h3>

            <p>
                Automate everyday library operations.
            </p>

        </div>

    </div>



    <div class="feature-item">

        <div class="feature-icon">
            📊
        </div>

        <div>

            <h3>
                Powerful Reports
            </h3>

            <p>
                Make better data-driven decisions.
            </p>

        </div>

    </div>


</section>



<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.querySelector(".password-toggle");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "🙈";

    } else {

        password.type = "password";

        button.textContent = "👁";

    }

}

</script>


</body>

</html>