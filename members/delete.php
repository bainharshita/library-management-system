<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid member ID.");
}

$sql = "SELECT COUNT(*) AS total
        FROM transactions
        WHERE member_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if ($data["total"] > 0) {
    die("This member cannot be deleted because transaction history exists.");
}

$sql = "DELETE FROM members WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    header("Location: manage.php");
    exit();

} else {

    die("Error deleting member: " . mysqli_error($conn));

}

?>