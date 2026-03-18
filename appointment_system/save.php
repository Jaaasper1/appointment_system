<?php
include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$service = $_POST['service'];
$date = $_POST['date'];
$time = $_POST['time'];

$sql = "INSERT INTO appointments (name, email, service, appointment_date, appointment_time)
        VALUES ('$name', '$email', '$service', '$date', '$time')";

if ($conn->query($sql) === TRUE) {
    echo "Appointment booked successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>