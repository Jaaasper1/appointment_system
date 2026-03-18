<?php
include 'config.php';
$id = $_GET['id'];

$conn->query("DELETE FROM appointments WHERE id=$id");

header("Location: admin.php");
?>