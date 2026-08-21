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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Book | Library Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include __DIR__ . "/../includes/sidebar.php"; ?>

<main>

    <div class="page-title">

        <h1>Add Book</h1>

        <p>Add a new book to your library collection.</p>

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
                    placeholder="Enter book title"
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
                    placeholder="Enter author name"
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
                    placeholder="Enter ISBN"
                    required
                >

            </div>


            <div class="form-group">

                <label for="quantity">
                    Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="1"
                    placeholder="Enter quantity"
                    required
                >

            </div>


            <div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Book
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