
<?php

session_start();

if (
    !isset($_SESSION["student_id"]) ||
    ($_SESSION["role"] ?? "") !== "student"
) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$studentId = (int) $_SESSION["student_id"];
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["member_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if ($name === "" || $email === "") {
        $message = "Name and email are required.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } else {

        // Check whether another student uses this email.
        $checkSql = "SELECT id FROM members
                     WHERE email = ? AND id != ?";

        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "si", $email, $studentId);
        mysqli_stmt_execute($checkStmt);

        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {

            $message = "This email is already registered.";
            $messageType = "error";

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
                $studentId
            );

            if (mysqli_stmt_execute($stmt)) {

                $_SESSION["member_name"] = $name;
                $_SESSION["email"] = $email;

                $message = "Profile updated successfully!";
                $messageType = "success";

            } else {

                $message = "Unable to update your profile.";
                $messageType = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($checkStmt);
    }
}

// Fetch the latest profile details.
$sql = "SELECT member_name, email, phone, address
        FROM members
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$student) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile | GreenShelf</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .profile-page {
            max-width: 760px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .profile-back {
            display: inline-block;
            margin-bottom: 25px;
            color: #34734a;
            text-decoration: none;
            font-weight: 600;
        }

        .profile-heading {
            margin-bottom: 25px;
        }

        .profile-heading h1 {
            color: #245b3c;
            margin-bottom: 8px;
        }

        .profile-heading p {
            color: #718074;
        }

        .profile-card {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #e7eee6;
            box-shadow: 0 8px 25px rgba(36, 91, 60, 0.06);
        }

        .profile-avatar {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: #e4f2e5;
            color: #245b3c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .profile-form .form-group {
            margin-bottom: 20px;
        }

        .profile-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #355340;
        }

        .profile-form input,
        .profile-form textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dce5dc;
            border-radius: 8px;
            font: inherit;
            box-sizing: border-box;
        }

        .profile-form textarea {
            min-height: 100px;
            resize: vertical;
        }

        .profile-form input:focus,
        .profile-form textarea:focus {
            outline: 2px solid #9dc7a5;
            border-color: #57956b;
        }

        .profile-message {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .profile-message.success {
            background: #e4f5e7;
            color: #28743b;
        }

        .profile-message.error {
            background: #fde8e7;
            color: #a33b35;
        }

        .profile-save {
            background: #245b3c;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .profile-save:hover {
            background: #19472d;
        }

        @media (max-width: 600px) {
            .profile-card {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<main class="profile-page">

    <a href="dashboard.php" class="profile-back">
        ← Back to Dashboard
    </a>

    <div class="profile-heading">
        <h1>👤 My Profile</h1>
        <p>View and manage your GreenShelf account details.</p>
    </div>

    <section class="profile-card">

        <div class="profile-avatar">
            <?php echo strtoupper(substr($student["member_name"], 0, 1)); ?>
        </div>

        <?php if ($message !== ""): ?>
            <div class="profile-message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="profile-form">

            <div class="form-group">
                <label for="member_name">Full Name</label>
                <input
                    type="text"
                    id="member_name"
                    name="member_name"
                    value="<?php echo htmlspecialchars($student["member_name"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($student["email"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($student["phone"] ?? ""); ?>"
                >
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea
                    id="address"
                    name="address"
                ><?php echo htmlspecialchars($student["address"] ?? ""); ?></textarea>
            </div>

            <button type="submit" class="profile-save">
                Save Changes
            </button>

        </form>

    </section>

</main>

</body>
</html>