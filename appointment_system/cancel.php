<?php
include 'config.php';
$id = $_GET['id'];

$conn->query("UPDATE appointments SET status='Cancelled' WHERE id=$id");

header("Location: admin.php");
?>