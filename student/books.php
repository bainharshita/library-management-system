
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

/* Handle book request */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bookId = (int) ($_POST["book_id"] ?? 0);

    if ($bookId <= 0) {

        $message = "Invalid book selected.";
        $messageType = "error";

    } else {

        // Check book availability.
        $sql = "SELECT id, available FROM books WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        mysqli_stmt_execute($stmt);

        $bookResult = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($bookResult);
        mysqli_stmt_close($stmt);

        if (!$book) {

            $message = "Book not found.";
            $messageType = "error";

        } elseif ((int) $book["available"] <= 0) {

            $message = "This book is currently unavailable.";
            $messageType = "error";

        } else {

            // Check for an existing pending request.
            $sql = "SELECT id FROM book_requests
                    WHERE member_id = ?
                    AND book_id = ?
                    AND status = 'Pending'
                    LIMIT 1";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $studentId, $bookId);
            mysqli_stmt_execute($stmt);

            $pendingResult = mysqli_stmt_get_result($stmt);
            $hasPending = mysqli_num_rows($pendingResult) > 0;

            mysqli_stmt_close($stmt);

            // Check whether the student already has this book.
            $sql = "SELECT id FROM transactions
                    WHERE member_id = ?
                    AND book_id = ?
                    AND status = 'Issued'
                    LIMIT 1";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $studentId, $bookId);
            mysqli_stmt_execute($stmt);

            $issuedResult = mysqli_stmt_get_result($stmt);
            $hasIssued = mysqli_num_rows($issuedResult) > 0;

            mysqli_stmt_close($stmt);

            if ($hasPending) {

                $message = "You already have a pending request for this book.";
                $messageType = "error";

            } elseif ($hasIssued) {

                $message = "You already have this book issued to you.";
                $messageType = "error";

            } else {

                $sql = "INSERT INTO book_requests (member_id, book_id)
                        VALUES (?, ?)";

                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $studentId, $bookId);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "Book request submitted successfully! Waiting for admin approval.";
                    $messageType = "success";
                } else {
                    $message = "Unable to submit your request. Please try again.";
                    $messageType = "error";
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

/* Fetch books */

$sql = "SELECT id, title, author, isbn, quantity, available
        FROM books
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

/* Fetch pending requests for this student */

$pendingBooks = [];

$sql = "SELECT book_id FROM book_requests
        WHERE member_id = ? AND status = 'Pending'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$pendingResult = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($pendingResult)) {
    $pendingBooks[] = (int) $row["book_id"];
}

mysqli_stmt_close($stmt);

/* Fetch books currently issued to this student */

$issuedBooks = [];

$sql = "SELECT book_id FROM transactions
        WHERE member_id = ? AND status = 'Issued'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);

$issuedResult = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($issuedResult)) {
    $issuedBooks[] = (int) $row["book_id"];
}

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Browse Books | GreenShelf</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .catalog-page {
            padding: 30px;
        }

        .catalog-heading {
            margin-bottom: 25px;
        }

        .catalog-heading h1 {
            color: #245b3c;
            margin-bottom: 8px;
        }

        .catalog-heading p {
            color: #718074;
        }

        .catalog-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .catalog-search {
            width: 100%;
            max-width: 500px;
            padding: 13px 16px;
            border: 1px solid #dce5dc;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
        }

        .catalog-search:focus {
            border-color: #4c8b5d;
        }

        .catalog-count {
            color: #718074;
            font-size: 14px;
        }

        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 22px;
        }

        .catalog-card {
            background: #fff;
            border: 1px solid #e7eee6;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .catalog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
        }

        .catalog-book-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf5eb;
            border-radius: 12px;
            font-size: 30px;
            margin-bottom: 18px;
        }

        .catalog-card h3 {
            color: #245b3c;
            font-size: 19px;
            margin: 0 0 10px;
            overflow-wrap: anywhere;
        }

        .catalog-author {
            color: #718074;
            margin-bottom: 16px;
        }

        .catalog-isbn {
            color: #718074;
            font-size: 13px;
            margin-bottom: 18px;
            overflow-wrap: anywhere;
        }

        .catalog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border-top: 1px solid #edf1eb;
            padding-top: 16px;
        }

        .catalog-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .catalog-available {
            background: #e4f5e7;
            color: #28743b;
        }

        .catalog-unavailable {
            background: #fde8e7;
            color: #a33b35;
        }

        .request-btn {
            width: 100%;
            margin-top: 18px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #245b3c;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .request-btn:hover {
            background: #19472d;
        }

        .request-btn:disabled {
            background: #e7eee6;
            color: #637568;
            cursor: not-allowed;
        }

        .catalog-message {
            padding: 14px 18px;
            border-radius: 9px;
            margin-bottom: 22px;
        }

        .catalog-message.success {
            background: #e4f5e7;
            color: #28743b;
        }

        .catalog-message.error {
            background: #fde8e7;
            color: #a33b35;
        }

        .catalog-empty {
            display: none;
            text-align: center;
            padding: 45px 20px;
            background: #fff;
            border-radius: 12px;
            color: #718074;
        }

        .catalog-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #34734a;
            text-decoration: none;
            font-weight: 600;
        }

        .catalog-requests-link {
            display: inline-block;
            margin-left: 18px;
            color: #34734a;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .catalog-page {
                padding: 20px;
            }

            .catalog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<main class="catalog-page">

    <a href="dashboard.php" class="catalog-back">
        ← Back to Dashboard
    </a>

    <a href="my-requests.php" class="catalog-requests-link">
        📋 My Requests
    </a>

    <div class="catalog-heading">
        <h1>📚 Browse Books</h1>
        <p>Explore the GreenShelf library collection.</p>
    </div>

    <?php if ($message !== ""): ?>
        <div class="catalog-message <?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="catalog-toolbar">

        <input
            type="text"
            id="catalogSearch"
            class="catalog-search"
            placeholder="🔎 Search by title, author, or ISBN..."
            autocomplete="off"
        >

        <span class="catalog-count" id="catalogCount">
            Loading books...
        </span>

    </div>

    <div class="catalog-grid" id="catalogGrid">

        <?php if ($result && mysqli_num_rows($result) > 0): ?>

            <?php while ($book = mysqli_fetch_assoc($result)): ?>

                <?php
                    $bookId = (int) $book["id"];
                    $isPending = in_array($bookId, $pendingBooks, true);
                    $isIssued = in_array($bookId, $issuedBooks, true);
                    $isAvailable = (int) $book["available"] > 0;
                ?>

                <article
                    class="catalog-card"
                    data-search="<?php
                        echo htmlspecialchars(
                            strtolower(
                                $book["title"] . " " .
                                $book["author"] . " " .
                                $book["isbn"]
                            ),
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>"
                >

                    <div class="catalog-book-icon">📖</div>

                    <h3>
                        <?php echo htmlspecialchars($book["title"]); ?>
                    </h3>

                    <p class="catalog-author">
                        By <?php echo htmlspecialchars($book["author"]); ?>
                    </p>

                    <p class="catalog-isbn">
                        ISBN: <?php echo htmlspecialchars($book["isbn"]); ?>
                    </p>

                    <div class="catalog-meta">

                        <span>
                            <strong><?php echo (int) $book["available"]; ?></strong>
                            of <?php echo (int) $book["quantity"]; ?> available
                        </span>

                        <?php if ($isAvailable): ?>
                            <span class="catalog-status catalog-available">
                                Available
                            </span>
                        <?php else: ?>
                            <span class="catalog-status catalog-unavailable">
                                Unavailable
                            </span>
                        <?php endif; ?>

                    </div>

                    <?php if ($isPending): ?>

                        <button class="request-btn" type="button" disabled>
                            ⏳ Request Pending
                        </button>

                    <?php elseif ($isIssued): ?>

                        <button class="request-btn" type="button" disabled>
                            ✓ Already Borrowed
                        </button>

                    <?php elseif ($isAvailable): ?>

                        <form method="POST">
                            <input
                                type="hidden"
                                name="book_id"
                                value="<?php echo $bookId; ?>"
                            >

                            <button type="submit" class="request-btn">
                                📩 Request Book
                            </button>
                        </form>

                    <?php else: ?>

                        <button class="request-btn" type="button" disabled>
                            Currently Unavailable
                        </button>

                    <?php endif; ?>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <p>No books have been added to the library yet.</p>

        <?php endif; ?>

    </div>

    <div class="catalog-empty" id="catalogEmpty">
        <h3>No books found 🔎</h3>
        <p>Try searching with another title, author, or ISBN.</p>
    </div>

</main>

<script>
    const searchInput = document.getElementById("catalogSearch");
    const cards = Array.from(document.querySelectorAll(".catalog-card"));
    const count = document.getElementById("catalogCount");
    const emptyMessage = document.getElementById("catalogEmpty");

    function filterBooks() {
        const query = searchInput.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach(card => {
            const searchableText = card.dataset.search || "";
            const matches = searchableText.includes(query);

            card.style.display = matches ? "" : "none";

            if (matches) {
                visible++;
            }
        });

        count.textContent = visible +
            (visible === 1 ? " book found" : " books found");

        emptyMessage.style.display = visible === 0 ? "block" : "none";
    }

    searchInput.addEventListener("input", filterBooks);

    filterBooks();
</script>

</body>
</html>