<?php
session_start();
require_once __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $sql = "SELECT id, username, password AS admin_password FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if (!$admin) {
    $error = "Username not found in database.";
} elseif (!password_verify($password, $admin["admin_password"])) {
    $error = "Invalid username or password.";
} else {

    $_SESSION["admin_id"] = $admin["id"];
    $_SESSION["username"] = $admin["username"];

    header("Location: dashboard.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management - Login</title>
</head>

<body>

<h1>Library Management System</h1>

<form method="POST">

    <input
        type="text"
        name="username"
        placeholder="Username"
        required
    >

    <br><br>

    <input
        type="password"
        name="password"
        placeholder="Password"
        required
    >

    <br><br>

    <button type="submit">Login</button>

</form>

<?php if ($error): ?>
    <p><?php echo $error; ?></p>
<?php endif; ?>

</body>
</html>