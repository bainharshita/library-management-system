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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Member | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title">

        <h1>Edit Member</h1>

        <p>Update member information.</p>

    </div>


    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="form-container">

        <form method="POST">


            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($member["member_name"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($member["email"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone Number
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($member["phone"]); ?>"
                >

            </div>


            <div class="form-group">

                <label for="address">
                    Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="4"
                ><?php echo htmlspecialchars($member["address"]); ?></textarea>

            </div>


            <div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save Changes
                </button>

                <a
                    href="manage.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</main>

</body>

</html>