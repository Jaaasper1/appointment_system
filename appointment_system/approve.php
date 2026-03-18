<?php
include 'config.php';
$id = $_GET['id'];

$conn->query("UPDATE appointments SET status='Approved' WHERE id=$id");

header("Location: admin.php");
?>