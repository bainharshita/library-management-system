<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$sql = "SELECT * FROM books ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Books | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title page-title-row">

        <div>

            <h1>Books</h1>

            <p>Manage your library collection.</p>

        </div>

        <a href="add.php" class="btn btn-primary">
            + Add Book
        </a>

    </div>


    <div class="search-box">

        <input
            type="text"
            id="bookSearch"
            placeholder="🔎 Search by title, author, ISBN..."
        >

    </div>


    <div class="table-container">

        <table id="booksTable">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Total</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php while ($book = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo $book["id"]; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($book["title"]); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($book["author"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($book["isbn"]); ?>
                    </td>

                    <td>
                        <?php echo $book["quantity"]; ?>
                    </td>

                    <td>
                        <?php echo $book["available"]; ?>
                    </td>

                    <td>

                        <?php if ($book["available"] > 0): ?>

                            <span class="status status-available">
                                Available
                            </span>

                        <?php else: ?>

                            <span class="status status-unavailable">
                                Unavailable
                            </span>

                        <?php endif; ?>

                    </td>

                    <td class="actions">

                        <a
                            href="edit.php?id=<?php echo $book["id"]; ?>"
                            class="btn btn-small btn-edit"
                        >
                            Edit
                        </a>

                        <a
                            href="delete.php?id=<?php echo $book["id"]; ?>"
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