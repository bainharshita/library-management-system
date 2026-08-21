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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Members | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <script src="../assets/js/script.js" defer></script>

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title page-title-row">

        <div>

            <h1>Members</h1>

            <p>Manage registered library members.</p>

        </div>

        <a href="add.php" class="btn btn-primary">
            + Add Member
        </a>

    </div>


    <div class="search-box">

        <input
            type="text"
            id="memberSearch"
            placeholder="🔎 Search by name, email, phone..."
        >

    </div>


    <div class="table-container">

        <table id="membersTable">

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

                    <td>
                        <?php echo $member["id"]; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($member["member_name"]); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($member["email"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($member["phone"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($member["address"]); ?>
                    </td>

                    <td class="actions">

                        <a
                            href="edit.php?id=<?php echo $member["id"]; ?>"
                            class="btn btn-small btn-edit"
                        >
                            Edit
                        </a>

                        <a
                            href="delete.php?id=<?php echo $member["id"]; ?>"
                            class="btn btn-small btn-delete"
                            onclick="return confirmDelete();"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</main>

</body>

</html>