<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if ($name == "" || $email == "") {

        $message = "Name and email are required.";

    } else {

        $sql = "INSERT INTO members (member_name, email, phone, address)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $name,
            $email,
            $phone,
            $address
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Member added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Member</title>
</head>

<body>

<h1>Add New Member</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">

    <label>Name</label><br>
    <input
        type="text"
        name="name"
        required
    >

    <br><br>

    <label>Email</label><br>
    <input
        type="email"
        name="email"
        required
    >

    <br><br>

    <label>Phone</label><br>
    <input
        type="text"
        name="phone"
    >

    <br><br>

    <label>Address</label><br>
    <textarea name="address"></textarea>

    <br><br>

    <button type="submit">Add Member</button>

</form>

<br>

<a href="manage.php">View Members</a>

</body>

</html>