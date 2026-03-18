<?php
include 'config.php';
$result = $conn->query("SELECT * FROM appointments ORDER BY created_at DESC");
?>

<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
?>
<h2>Appointments</h2>

<table class="table table-bordered">
<tr>
    <th>Name</th>
    <th>Service</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['service'] ?></td>
    <td><?= $row['appointment_date'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <a href="approve.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
        <a href="cancel.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Cancel</a>
        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>