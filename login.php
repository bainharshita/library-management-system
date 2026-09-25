
<?php

session_start();

require_once __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Please enter your username/email and password.";

    } else {

        /*
        |--------------------------------------------------
        | 1. CHECK ADMIN ACCOUNT
        |--------------------------------------------------
        */

        $sql = "SELECT id, username, password
                FROM admins
                WHERE username = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $username);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $admin = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);


        if ($admin && password_verify($password, $admin["password"])) {

            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["username"] = $admin["username"];
            $_SESSION["role"] = "admin";

            header("Location: dashboard.php");
            exit();

        }


        /*
        |--------------------------------------------------
        | 2. CHECK STUDENT ACCOUNT
        |--------------------------------------------------
        */

        $sql = "SELECT id, member_name, email, password
                FROM members
                WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $username);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $student = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);


        if ($student && !empty($student["password"])
            && password_verify($password, $student["password"])) {

            session_regenerate_id(true);

            $_SESSION["student_id"] = $student["id"];
            $_SESSION["member_name"] = $student["member_name"];
            $_SESSION["email"] = $student["email"];
            $_SESSION["role"] = "student";

            header("Location: student/dashboard.php");
            exit();

        }


        /*
        |--------------------------------------------------
        | 3. INVALID CREDENTIALS
        |--------------------------------------------------
        */

        $error = "Incorrect username/email or password.";

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>GreenShelf | Library Management System</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body class="landing-page">


<!-- NAVBAR -->

<header class="landing-navbar">

    <div class="brand">

        <div class="brand-icon">
            📖
        </div>

        <div>

            <h2>Green<span>Shelf</span></h2>

            <p>Library Management System</p>

        </div>

    </div>


    <nav>

        <a href="#home">Home</a>

       <a href="#" onclick="openInfoModal('aboutModal'); return false;">About</a>

        <a href="#features">Features</a>

        <a href="harshitab25805@gmail.com" onclick="openInfoModal('contactModal'); return false;">Contact</a>

        <a href="#login" class="nav-login">
            Sign In
        </a>

    </nav>

</header>


<!-- HERO -->

<section class="hero" id="home">


    <!-- LEFT CONTENT -->

    <div class="hero-content">

        <div class="hero-badge">
            Smart Library. Simplified.
        </div>


        <h1>

            Revolutionize Your Library

            <br>

            with the
            <span>Best Library</span>

            <br>

            Management Software

        </h1>


        <p class="hero-description">

            Effortlessly manage your library from book
            collections to member records, transactions
            and reporting, all in one place.

        </p>


        <div class="hero-line"></div>


        <p class="hero-subtext">

            Optimize your library operations with our
            feature-rich, user-friendly management system
            built for modern libraries.

        </p>


        <div class="hero-buttons">

            <a
                href="#login"
                class="hero-primary"
            >
                🌿 Get Started Now
            </a>


            <a href="#demo" class="hero-demo" id="watchDemo">
                <span class="play-button">▶</span>
                Watch Demo
            </a>

        </div>


        <!-- SIMPLE BOOK ILLUSTRATION -->

        <div class="book-illustration">

            <div class="book book-one">
                GreenShelf
            </div>

            <div class="book book-two">
                Library
            </div>

            <div class="book book-three">
                Knowledge
            </div>

            <div class="plant">

                <span>🌿</span>

            </div>

        </div>

    </div>



    <!-- LOGIN CARD -->

    <div class="login-wrapper" id="login">

        <div class="login-card">


            <div class="login-icon">
                📖
            </div>


            <h2>
                Welcome Back
            </h2>


            <p class="login-subtitle">

                Sign in to access your library dashboard

            </p>


            <?php if ($error !== ""): ?>

                <div class="login-error">

                    ⚠️
                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="login-field">

                    <label for="username">
                        Username
                    </label>

                    <div class="input-wrapper">

                        <span>👤</span>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username or email"
                            value="<?php echo htmlspecialchars($_POST["username"] ?? ""); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="login-field">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <span>🔒</span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            👁
                        </button>

                    </div>

                </div>


                <div class="login-options">

                    <label>

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        Remember me

                    </label>


                    <a href="#">
                        Forgot Password?
                    </a>

                </div>


                <button
                    type="submit"
                    class="login-button"
                >

                    🔐 Sign In

                </button>


            </form>


            <div class="login-divider">

                <span></span>

                <p>or</p>

                <span></span>

            </div>


            <p class="admin-note">

                Don't have an account?

                <a href="signup.php">
                     Sign Up
                </a>

            </p>


        </div>

    </div>

</section>

<!-- WATCH DEMO MODAL -->
<div class="demo-overlay" id="demoOverlay" aria-hidden="true">

    <div class="demo-modal" role="dialog" aria-modal="true"
         aria-labelledby="demoTitle">

        <button type="button" class="demo-close" id="demoClose"
                aria-label="Close demo">&times;</button>

        <div class="demo-header">
            <span class="demo-badge">GREENSHELF WALKTHROUGH</span>
            <h2 id="demoTitle">Explore GreenShelf</h2>
            <p id="demoDescription">
                Your library, organized in one place.
            </p>
        </div>

        <div class="demo-screen">
            <div class="demo-screen-icon" id="demoIcon">📊</div>
            <h3 id="demoScreenTitle">Dashboard</h3>
            <p id="demoScreenText">
                Get a quick overview of books, members, issued books,
                and overdue returns from one dashboard.
            </p>
            <div class="demo-preview" id="demoPreview">
                <div class="demo-stat">
                    <span>Total Books</span>
                    <strong>1,250</strong>
                </div>
                <div class="demo-stat">
                    <span>Members</span>
                    <strong>320</strong>
                </div>
                <div class="demo-stat">
                    <span>Books Issued</span>
                    <strong>86</strong>
                </div>
            </div>
        </div>

        <div class="demo-footer">
            <span id="demoStep">Step 1 of 4</span>

            <div class="demo-actions">
                <button type="button" id="demoPrev" class="demo-prev"
                        disabled>Back</button>
                <button type="button" id="demoNext" class="demo-next">
                    Next →
                </button>
            </div>
        </div>

    </div>
</div>

<!-- FEATURES -->

<section
    class="feature-strip"
    id="features"
>
    <div class="feature-heading">
        <span>WHAT MAKES US DIFFERENT</span>
        <h2>Why GreenShelf?</h2>
        <p>
            Everything you need for a simpler, smarter library experience.
        </p>
    </div>


    <div class="feature-item">

        <div class="feature-icon">
            🛡️
        </div>

        <div>

            <h3>
                Secure & Reliable
            </h3>

            <p>
                Your library data stays protected.
            </p>

        </div>

    </div>



    <div class="feature-item">

        <div class="feature-icon">
            ⏱️
        </div>

        <div>

            <h3>
                Save Time
            </h3>

            <p>
                Automate everyday library operations.
            </p>

        </div>

    </div>



    
<div class="feature-item">
    <div class="feature-icon">📚</div>
    <div>
        <h3>Smart Book Management</h3>
        <p>Browse books by title, author, or ISBN and check availability before requesting a book.</p>
    </div>
</div>

<div class="feature-item">
    <div class="feature-icon">📋</div>
    <div>
        <h3>Easy Book Requests</h3>
        <p>Submit book requests online and track pending, approved, or rejected requests.</p>
    </div>
</div>

<div class="feature-item">
    <div class="feature-icon">📅</div>
    <div>
        <h3>Issue & Return Tracking</h3>
        <p>Track borrowed books, issue dates, due dates, and return status in one place.</p>
    </div>
</div>

<div class="feature-item">
    <div class="feature-icon">💰</div>
    <div>
        <h3>Fine Management</h3>
        <p>View outstanding fines and late-return charges directly from your account.</p>
    </div>
</div>

        <div class="feature-item">
            <div class="feature-icon">🔐</div>
            <div>
                <h3>Secure Role-Based Access</h3>
                <p>Separate student and administrator accounts with features tailored to each role.</p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-icon">📊</div>
            <div>
                <h3>Personalized Dashboard</h3>
                <p>Access your books, requests, fines, and account details from one convenient dashboard.</p>
            </div>
        </div>

    </div>


</section>


<!-- ABOUT MODAL -->

<div class="info-modal" id="aboutModal">
    <div class="info-modal-content">

        <button class="modal-close"
                onclick="closeInfoModal('aboutModal')">
            &times;
        </button>

        <div class="modal-icon">🌿</div>

        <h2>About GreenShelf</h2>

        <p class="modal-subtitle">
            A smarter way to manage your library.
        </p>

        <p>
            GreenShelf is a library management system designed
            to simplify everyday library operations and make
            book management more efficient.
        </p>

        <p>
            From browsing books and submitting requests to
            tracking borrowed books and managing fines,
            GreenShelf brings essential library services
            together in one convenient platform.
        </p>

        <div class="about-highlights">
            <div>
                <span>📚</span>
                <h4>Easy Book Access</h4>
                <p>Browse and discover books with ease.</p>
            </div>

            <div>
                <span>⚡</span>
                <h4>Simple Management</h4>
                <p>Keep library operations organized.</p>
            </div>

            <div>
                <span>🔐</span>
                <h4>Role-Based Access</h4>
                <p>Dedicated access for students and admins.</p>
            </div>
        </div>

        <button class="modal-primary-btn"
                onclick="closeInfoModal('aboutModal')">
            Got it
        </button>

    </div>
</div>


<!-- CONTACT MODAL -->

<div class="info-modal" id="contactModal">
    <div class="info-modal-content">

        <button class="modal-close"
                onclick="closeInfoModal('contactModal')">
            &times;
        </button>

        <div class="modal-icon">✉️</div>

        <h2>Contact Us</h2>

        <p class="modal-subtitle">
            We'd love to hear from you.
        </p>

        
    <form id="contactForm" action="contact.php" method="POST">

        <div class="contact-form-group">
            <label for="contactName">Your Name</label>
            <input
                type="text"
                id="contactName"
                name="name"
                placeholder="Enter your name"
                maxlength="100"
                required
            >
        </div>

        <div class="contact-form-group">
            <label for="contactEmail">Email Address</label>
            <input
                type="email"
                id="contactEmail"
                name="email"
                placeholder="Enter your email"
                maxlength="254"
                required
            >
        </div>

        <div class="contact-form-group">
            <label for="contactMessage">Message</label>
            <textarea
                id="contactMessage"
                name="message"
                placeholder="How can we help you?"
                rows="4"
                maxlength="5000"
                required
            ></textarea>
        </div>

        <button type="submit" class="modal-primary-btn" id="contactSubmit">
            Send Message
        </button>

        <p id="contactStatus" role="status" aria-live="polite"></p>

    </form>

        <p class="contact-note">
            Have a question or suggestion? We're here to help.
        </p>

    </div>
</div>


<!-- FOOTER -->

<footer class="landing-footer" id="contact">

    <div class="footer-content">

        <div class="footer-brand">
            <h2>📚 GreenShelf</h2>
            <p>
                Making library management simpler, smarter,
                and more accessible for everyone.
            </p>
        </div>

        <div class="footer-links">
            <h3>Quick Links</h3>
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#features">Features</a>
            <a href="#login">Sign In</a>
        </div>

        <div class="footer-links">
            <h3>Explore</h3>
            <a href="#features">Book Management</a>
            <a href="#features">Book Requests</a>
            <a href="#features">Fine Management</a>
        </div>

        <div class="footer-contact">
            <h3>Get in Touch</h3>
            <p>Have questions about GreenShelf?</p>
            <a href="harshitab25805@gmail.com" onclick="openInfoModal('contactModal'); return false;">
                Contact Support
            </a>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© <?php echo date("Y"); ?> GreenShelf. All rights reserved.</p>
        <p>Designed for a smarter library experience 🌱</p>
    </div>

</footer>


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.querySelector(".password-toggle");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "🙈";

    } else {

        password.type = "password";

        button.textContent = "👁";

    }

}

// ================================
// WATCH DEMO WALKTHROUGH
// ================================

const demoOverlay = document.getElementById("demoOverlay");
const demoOpen = document.getElementById("watchDemo");
const demoClose = document.getElementById("demoClose");

const demoTitle = document.getElementById("demoScreenTitle");
const demoText = document.getElementById("demoScreenText");
const demoIcon = document.getElementById("demoIcon");
const demoPreview = document.getElementById("demoPreview");
const demoStep = document.getElementById("demoStep");
const demoPrev = document.getElementById("demoPrev");
const demoNext = document.getElementById("demoNext");

const demoSlides = [
    {
        icon: "📊",
        title: "Dashboard",
        text: "Get a quick overview of books, members, issued books, and overdue returns from one dashboard.",
        preview: [
            ["Total Books", "1,250"],
            ["Members", "320"],
            ["Books Issued", "86"]
        ]
    },
    {
        icon: "📚",
        title: "Book Management",
        text: "Keep your collection organized. Add books, update book details, track quantities, and check availability.",
        preview: [
            ["Book Titles", "850"],
            ["Available", "1,164"],
            ["Categories", "12"]
        ]
    },
    {
        icon: "👥",
        title: "Member Management",
        text: "Manage student records, view member details, and keep your library's membership information organized.",
        preview: [
            ["Members", "320"],
            ["Active", "298"],
            ["New This Month", "22"]
        ]
    },
    {
        icon: "🔄",
        title: "Issue & Return",
        text: "Track issued books, monitor due dates, record returns, and calculate late fines automatically.",
        preview: [
            ["Issued", "86"],
            ["Returned Today", "14"],
            ["Overdue", "8"]
        ]
    }
];

let currentDemoSlide = 0;

function renderDemoSlide() {
    const slide = demoSlides[currentDemoSlide];

    demoIcon.textContent = slide.icon;
    demoTitle.textContent = slide.title;
    demoText.textContent = slide.text;
    demoStep.textContent =
        `Step ${currentDemoSlide + 1} of ${demoSlides.length}`;

    demoPreview.innerHTML = slide.preview.map(item => `
        <div class="demo-stat">
            <span>${item[0]}</span>
            <strong>${item[1]}</strong>
        </div>
    `).join("");

    demoPrev.disabled = currentDemoSlide === 0;

    demoNext.textContent =
        currentDemoSlide === demoSlides.length - 1
            ? "Finish ✓"
            : "Next →";
}

function openDemo() {
    currentDemoSlide = 0;
    renderDemoSlide();
    demoOverlay.classList.add("active");
    demoOverlay.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    demoClose.focus();
}

function closeDemo() {
    demoOverlay.classList.remove("active");
    demoOverlay.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    demoOpen.focus();
}

demoOpen.addEventListener("click", function (event) {
    event.preventDefault();
    openDemo();
});

demoClose.addEventListener("click", closeDemo);

demoOverlay.addEventListener("click", function (event) {
    if (event.target === demoOverlay) {
        closeDemo();
    }
});

demoPrev.addEventListener("click", function () {
    if (currentDemoSlide > 0) {
        currentDemoSlide--;
        renderDemoSlide();
    }
});

demoNext.addEventListener("click", function () {
    if (currentDemoSlide < demoSlides.length - 1) {
        currentDemoSlide++;
        renderDemoSlide();
    } else {
        closeDemo();
    }
});

document.addEventListener("keydown", function (event) {
    if (!demoOverlay.classList.contains("active")) return;

    if (event.key === "Escape") {
        closeDemo();
    }
});



function openInfoModal(modalId) {
    const modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
    }
}

function closeInfoModal(modalId) {
    const modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.remove("active");
        document.body.style.overflow = "";
    }
}

// Close when clicking outside the popup
document.querySelectorAll(".info-modal").forEach(modal => {
    modal.addEventListener("click", function(event) {
        if (event.target === modal) {
            closeInfoModal(modal.id);
        }
    });
});

// Close with Escape key
document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        document.querySelectorAll(".info-modal.active").forEach(modal => {
            closeInfoModal(modal.id);
        });
    }
});

// Contact form demo behavior


const contactForm = document.getElementById("contactForm");
const contactStatus = document.getElementById("contactStatus");
const contactSubmit = document.getElementById("contactSubmit");

contactForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    contactSubmit.disabled = true;
    contactSubmit.textContent = "Sending...";
    contactStatus.textContent = "";

    try {
        const response = await fetch("contact.php", {
            method: "POST",
            body: new FormData(contactForm)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Unable to send your message.");
        }

        contactStatus.textContent = result.message;
        contactStatus.style.color = "#2e6b45";

        contactForm.reset();

    } catch (error) {
        contactStatus.textContent =
            error.message || "Something went wrong. Please try again.";

        contactStatus.style.color = "#c0392b";

    } finally {
        contactSubmit.disabled = false;
        contactSubmit.textContent = "Send Message";
    }
});

</script>


</body>

</html>