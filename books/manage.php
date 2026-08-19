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
<html>

<head>
    <title>Manage Books</title>
    <script src="../assets/js/script.js"></script>
</head>

<body>

<h1>Manage Books</h1>

<a href="add.php">+ Add Book</a>

<br><br>
<input
    type="text"
    id="bookSearch"
    placeholder="Search books..."
>

<table id="booksTable" border="1" cellpadding="10">

   <thead>

    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Category</th>
        <th>Total</th>
        <th>Available</th>
        <th>Actions</th>
    </tr>

</thead>

<tbody>

<?php while ($book = mysqli_fetch_assoc($result)): ?>

    <tr>

        <td><?php echo $book["id"]; ?></td>

        <td><?php echo htmlspecialchars($book["title"]); ?></td>

        <td><?php echo htmlspecialchars($book["author"]); ?></td>

        <td><?php echo htmlspecialchars($book["isbn"]); ?></td>

        <td><?php echo htmlspecialchars($book["category"]); ?></td>

        <td><?php echo $book["quantity"]; ?></td>

        <td><?php echo $book["available"]; ?></td>

        <td>

            <a href="edit.php?id=<?php echo $book["id"]; ?>">
                Edit
            </a>

            |

            <a
                href="delete.php?id=<?php echo $book["id"]; ?>"
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