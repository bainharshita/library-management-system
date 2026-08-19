<?php

$current_page = basename($_SERVER["PHP_SELF"]);

?>

<aside>

    <h2>📚 Library Management</h2>

    <nav>

        <a href="/library-management/dashboard.php">
            🏠 Dashboard
        </a>

        <h4>Books</h4>

        <a href="/library-management/books/manage.php">
            📖 All Books
        </a>

        <a href="/library-management/books/add.php">
            ➕ Add Book
        </a>

        <h4>Members</h4>

        <a href="/library-management/members/manage.php">
            👥 All Members
        </a>

        <a href="/library-management/members/add.php">
            ➕ Add Member
        </a>

        <h4>Transactions</h4>

        <a href="/library-management/transactions/issue.php">
            📤 Issue Book
        </a>

        <a href="/library-management/transactions/return.php">
            📥 Return Book
        </a>

        <a href="/library-management/transactions/history.php">
            📋 History
        </a>

        <br>

        <a href="/library-management/logout.php">
            🚪 Logout
        </a>

    </nav>

</aside>