<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid member ID.");
}

$sql = "SELECT id, member_name, email, phone, address
        FROM members
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$member = mysqli_fetch_assoc($result);



if (!$member) {
    die("Member not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if ($name == "" || $email == "") {

        $message = "Name and email are required.";

    } else {

        $sql = "UPDATE members
                SET member_name = ?, email = ?, phone = ?, address = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $name,
            $email,
            $phone,
            $address,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Member updated successfully!";

            $member["member_name"] = $name;
            $member["email"] = $email;
            $member["phone"] = $phone;
            $member["address"] = $address;

        } else {

            $message = "Error: " . mysqli_error($conn);

        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Member</title>
</head>

<body>

<h1>Edit Member</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">

    <label>Name</label><br>

    <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($member["member_name"]); ?>"
        required
    >

    <br><br>

    <label>Email</label><br>

    <input
        type="email"
        name="email"
        value="<?php echo htmlspecialchars($member["email"]); ?>"
        required
    >

    <br><br>

    <label>Phone</label><br>

    <input
        type="text"
        name="phone"
        value="<?php echo htmlspecialchars($member["phone"]); ?>"
    >

    <br><br>

    <label>Address</label><br>

    <textarea name="address"><?php echo htmlspecialchars($member["address"]); ?></textarea>

    <br><br>

    <button type="submit">Update Member</button>

</form>

<br>

<a href="manage.php">Back to Members</a>

</body>

</html>