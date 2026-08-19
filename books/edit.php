<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid book ID.");
}

$sql = "SELECT * FROM books WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    die("Book not found.");
}

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

        $issued = $book["quantity"] - $book["available"];

        if ($quantity < $issued) {
            $message = "Quantity cannot be less than currently issued copies.";
        } else {

            $available = $quantity - $issued;

            $sql = "UPDATE books
                    SET title = ?, author = ?, isbn = ?,
                        category = ?, quantity = ?, available = ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssssiii",
                $title,
                $author,
                $isbn,
                $category,
                $quantity,
                $available,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Book updated successfully!";

                $book["title"] = $title;
                $book["author"] = $author;
                $book["isbn"] = $isbn;
                $book["category"] = $category;
                $book["quantity"] = $quantity;
                $book["available"] = $available;

            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Book</title>
</head>

<body>

<h1>Edit Book</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">

    <label>Book Title</label><br>
    <input
        type="text"
        name="title"
        value="<?php echo htmlspecialchars($book["title"]); ?>"
        required
    >

    <br><br>

    <label>Author</label><br>
    <input
        type="text"
        name="author"
        value="<?php echo htmlspecialchars($book["author"]); ?>"
        required
    >

    <br><br>

    <label>ISBN</label><br>
    <input
        type="text"
        name="isbn"
        value="<?php echo htmlspecialchars($book["isbn"]); ?>"
    >

    <br><br>

    <label>Category</label><br>
    <input
        type="text"
        name="category"
        value="<?php echo htmlspecialchars($book["category"]); ?>"
    >

    <br><br>

    <label>Quantity</label><br>
    <input
        type="number"
        name="quantity"
        min="1"
        value="<?php echo $book["quantity"]; ?>"
        required
    >

    <br><br>

    <button type="submit">Update Book</button>

</form>

<br>

<a href="manage.php">Back to Books</a>

</body>

</html>