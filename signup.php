
<?php

require_once __DIR__ . "/config/db.php";

$error = "";
$success = "";

$member_name = "";
$email = "";
$phone = "";
$address = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $member_name = trim($_POST["member_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Validate required fields
    if (
        $member_name === "" ||
        $email === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {
        $error = "Please fill in all required fields.";
    }

    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    // Validate name
    elseif (strlen($member_name) < 2) {
        $error = "Please enter a valid full name.";
    }

    // Validate password
    elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    }

    // Confirm password
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    else {

        // Check whether email already exists
        $sql = "SELECT id FROM members WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "An account with this email already exists.";

        } else {

            // Hash password before storing
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert student account
            $sql = "INSERT INTO members
                    (member_name, email, phone, address, password)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $member_name,
                $email,
                $phone,
                $address,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $success = "Account created successfully! You can now sign in.";

                // Clear form fields after successful signup
                $member_name = "";
                $email = "";
                $phone = "";
                $address = "";

            } else {

                $error = "Unable to create your account. Please try again.";

            }

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Signup | GreenShelf</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="signup-body">

<div class="signup-container">

    <!-- BRAND -->

    <div class="signup-brand">

        <div class="signup-logo">📖</div>

        <h1>Green<span>Shelf</span></h1>

        <p>Library Management System</p>

    </div>

    <!-- SIGNUP CARD -->

    <div class="signup-card">

        <a href="login.php" class="back-login">
            ← Back to Login
        </a>

        <h2>Join GreenShelf</h2>

        <p class="signup-subtitle">
            Create your student account and explore your library.
        </p>

        <?php if ($error !== ""): ?>

            <div class="signup-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <?php if ($success !== ""): ?>

            <div class="signup-success">
                ✓ <?php echo htmlspecialchars($success); ?>

                <br><br>

                <a href="login.php">Continue to Login →</a>
            </div>

        <?php endif; ?>

        <form method="POST" action="signup.php">

            <div class="signup-field">

                <label for="member_name">Full Name *</label>

                <input
                    type="text"
                    id="member_name"
                    name="member_name"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($member_name); ?>"
                    maxlength="100"
                    required
                >

            </div>

            <div class="signup-field">

                <label for="email">Email Address *</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($email); ?>"
                    maxlength="150"
                    required
                >

            </div>

            <div class="signup-field">

                <label for="phone">Phone Number</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="Enter your phone number"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    maxlength="20"
                >

            </div>

            <div class="signup-field">

                <label for="address">Address</label>

                <input
                    type="text"
                    id="address"
                    name="address"
                    placeholder="Enter your address"
                    value="<?php echo htmlspecialchars($address); ?>"
                    maxlength="255"
                >

            </div>

            <div class="signup-field">

                <label for="password">Password *</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    minlength="8"
                    required
                >

                <small>Use at least 8 characters.</small>

            </div>

            <div class="signup-field">

                <label for="confirm_password">Confirm Password *</label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    minlength="8"
                    required
                >

            </div>

            <button type="submit" class="signup-button">
                Create Student Account
            </button>

        </form>

        <div class="signup-security">
            🔒 Your password is hashed before it is stored.
        </div>

    </div>

</div>

</body>
</html>