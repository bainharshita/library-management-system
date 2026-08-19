<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$sql = "SELECT id, member_name, email, phone, address
        FROM members
        ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Members</title>
    <script src="../assets/js/script.js"></script>
</head>

<body>

<h1>Manage Members</h1>

<a href="add.php">+ Add Member</a>

<br><br>

<input
    type="text"
    id="memberSearch"
    placeholder="Search members..."
>

<br><br>

<table id="membersTable" border="1" cellpadding="10">

    <thead>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>

    </thead>

    <tbody>

    <?php while ($member = mysqli_fetch_assoc($result)): ?>

        <tr>

            <td><?php echo $member["id"]; ?></td>

            <td><?php echo htmlspecialchars($member["member_name"]); ?></td>

            <td><?php echo htmlspecialchars($member["email"]); ?></td>

            <td><?php echo htmlspecialchars($member["phone"]); ?></td>

            <td><?php echo htmlspecialchars($member["address"]); ?></td>

            <td>

                <a href="edit.php?id=<?php echo $member["id"]; ?>">
                    Edit
                </a>

                |

                <a
                    href="delete.php?id=<?php echo $member["id"]; ?>"
                    onclick="return confirmDelete();"
                >
                    Delete
                </a>

            </td>

        </tr>

    <?php endwhile; ?>

    </tbody>

</table>

<br>

<a href="../dashboard.php">Back to Dashboard</a>

</body>

</html>