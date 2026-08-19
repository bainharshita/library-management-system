<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $isbn = trim($_POST["isbn"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $quantity = (int)($_POST["quantity"] ?? 0);

    if ($title == "" || $author == "" || $quantity <= 0) {

        $message = "Please enter valid book details.";

    } else {

        $sql = "INSERT INTO books 
                (title, author, isbn, category, quantity, available)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssii",
            $title,
            $author,
            $isbn,
            $category,
            $quantity,
            $quantity
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Book added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Book</title>
</head>

<body>

<h1>Add New Book</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">

    <label>Book Title</label><br>
    <input type="text" name="title" required>

    <br><br>

    <label>Author</label><br>
    <input type="text" name="author" required>

    <br><br>

    <label>ISBN</label><br>
    <input type="text" name="isbn">

    <br><br>

    <label>Category</label><br>
    <input type="text" name="category">

    <br><br>

    <label>Quantity</label><br>
    <input type="number" name="quantity" min="1" required>

    <br><br>

    <button type="submit">Add Book</button>

</form>

<br>

<a href="manage.php">View Books</a>

</body>

</html>