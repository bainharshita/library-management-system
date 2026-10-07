<?php

$current_page = basename($_SERVER["PHP_SELF"]);

?>

<button class="admin-menu-toggle" id="adminMenuToggle" aria-label="Open menu">
    ☰
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php if ($current_page !== "dashboard.php"): ?>
    <a href="/library-management/dashboard.php" class="back-dashboard-btn">
        ← Back to Dashboard
    </a>
<?php endif; ?>

<button
    type="button"
    class="mobile-menu-toggle"
    id="mobileMenuToggle"
    aria-label="Open navigation menu"
    aria-expanded="false"
>
    ☰
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar">

    <div class="sidebar-logo">

        <div class="logo-icon">📚</div>

        <div>
            <h2>Green Shelf</h2>
            <span>Library Management System</span>
        </div>

    </div>


    <nav class="sidebar-nav">

        <p class="nav-heading">MAIN</p>

        <a
            href="/library-management/dashboard.php"
            class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"
        >
            <span>🏠</span>
            Dashboard
        </a>


        <p class="nav-heading">LIBRARY</p>

        <a href="/library-management/books/manage.php">
            <span>📖</span>
            Books
        </a>

        <a href="/library-management/books/add.php">
            <span>➕</span>
            Add Book
        </a>

        <a href="/library-management/members/manage.php">
            <span>👥</span>
            Members
        </a>

        <a href="/library-management/members/add.php">
            <span>➕</span>
            Add Member
        </a>

        <a href="/library-management/book-requests.php">
            <span>📋</span>
            Book Requests
        </a>

        <a href="/library-management/add-admin.php" class="nav-link">
            <span>🛡️</span>
            <span>Add Admin</span>
        </a>


        <p class="nav-heading">TRANSACTIONS</p>

        <a href="/library-management/transactions/issue.php">
            <span>📤</span>
            Issue Book
        </a>

        <a href="/library-management/transactions/return.php">
            <span>📥</span>
            Return Book
        </a>

        <a href="/library-management/transactions/history.php">
            <span>📋</span>
            History
        </a>

    </nav>


    <div class="sidebar-bottom">

        <a href="/library-management/logout.php" class="nav-link">
            <span>🚪</span>
            Logout
        </a>

    </div>

</aside>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("mobileMenuToggle");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (!toggle || !sidebar || !overlay) return;

    function closeMenu() {
        sidebar.classList.remove("sidebar-open");
        overlay.classList.remove("overlay-visible");
        toggle.setAttribute("aria-expanded", "false");
        toggle.setAttribute("aria-label", "Open navigation menu");
        toggle.textContent = "☰";
    }

    toggle.addEventListener("click", function () {
        const isOpen = sidebar.classList.toggle("sidebar-open");

        overlay.classList.toggle("overlay-visible", isOpen);
        toggle.setAttribute("aria-expanded", String(isOpen));
        toggle.setAttribute(
            "aria-label",
            isOpen ? "Close navigation menu" : "Open navigation menu"
        );
        toggle.textContent = isOpen ? "✕" : "☰";
    });

    overlay.addEventListener("click", closeMenu);

    sidebar.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", closeMenu);
    });
});
</script>