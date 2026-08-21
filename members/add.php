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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Member | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title">

        <h1>Add Member</h1>

        <p>Register a new member in the library.</p>

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
                    placeholder="Enter member name"
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
                    placeholder="Enter email address"
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
                    placeholder="Enter phone number"
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
                    placeholder="Enter member address"
                ></textarea>

            </div>


            <div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    + Add Member
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