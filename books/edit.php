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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Book | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title">

        <h1>Edit Book</h1>

        <p>Update the details of this book.</p>

    </div>


    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="form-container">

        <form method="POST">

            <div class="form-group">

                <label for="title">
                    Book Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php echo htmlspecialchars($book["title"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="author">
                    Author
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    value="<?php echo htmlspecialchars($book["author"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="isbn">
                    ISBN
                </label>

                <input
                    type="text"
                    id="isbn"
                    name="isbn"
                    value="<?php echo htmlspecialchars($book["isbn"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="quantity">
                    Total Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="1"
                    value="<?php echo htmlspecialchars($book["quantity"]); ?>"
                    required
                >

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