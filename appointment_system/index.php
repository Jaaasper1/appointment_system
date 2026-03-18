<?php
session_start();
include 'config.php';

// LOGIN
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid login!";
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// BOOK
if (isset($_POST['book'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    if ($conn->query("INSERT INTO appointments (name,email,service,appointment_date,appointment_time)
        VALUES ('$name','$email','$service','$date','$time')")) {
        $success = "Appointment booked successfully!";
    } else {
        $error = "Something went wrong!";
    }
}

// ACTIONS
if (isset($_GET['approve'])) {
    $conn->query("UPDATE appointments SET status='Approved' WHERE id=".$_GET['approve']);
}
if (isset($_GET['cancel'])) {
    $conn->query("UPDATE appointments SET status='Cancelled' WHERE id=".$_GET['cancel']);
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM appointments WHERE id=".$_GET['delete']);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Appointment System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
}

.navbar {
    background: rgba(0,0,0,0.7) !important;
}

.split-container {
    border-radius: 20px;
    overflow: hidden;
}

.split-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(85%);
}

.form-section {
    background: white;
}

#suggestions div:hover {
    background: #f1f1f1;
    cursor: pointer;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark px-4">
    <span class="navbar-brand">Appointment System</span>

    <?php if (!isset($_SESSION['admin'])): ?>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                Admin
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3">
                <form method="POST">
                    <input class="form-control mb-2" name="username" placeholder="Username" required>
                    <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
                    <button name="login" class="btn btn-dark w-100">Login</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <a href="?logout=true" class="btn btn-danger">Logout</a>
    <?php endif; ?>
</nav>

<div class="container mt-5">

<?php if (!isset($_SESSION['admin'])): ?>

    <!-- SPLIT FORM -->
    <div class="row shadow-lg split-container">

        <div class="col-md-6 p-0">
            <img src="picture/pic.png" class="split-img">
        </div>

        <div class="col-md-6 form-section p-5">

            <h3 class="text-center mb-4">Book Appointment</h3>

            <?php if(isset($success)): ?>
                <div id="successMsg" class="alert alert-success text-center">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input class="form-control mb-3" name="name" placeholder="Your Name" required>
                <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>

                <select class="form-control mb-3" name="service" required>
                    <option value="">Select Service</option>
                    <option>Consultation</option>
                    <option>Dental</option>
                    <option>Therapy</option>
                </select>

                <input class="form-control mb-3" type="date" name="date" required>
                <input class="form-control mb-3" type="time" name="time" required>

                <button name="book" class="btn btn-primary w-100">
                    <i class="fa fa-calendar-check"></i> Book Appointment
                </button>
            </form>

        </div>
    </div>

<?php else: ?>

    <h4 class="text-white mb-3">Welcome, <?= $_SESSION['admin']; ?> 👋</h4>

    <?php
    $total = $conn->query("SELECT COUNT(*) as total FROM appointments")->fetch_assoc()['total'];
    $pending = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status='Pending'")->fetch_assoc()['total'];
    $approved = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status='Approved'")->fetch_assoc()['total'];
    ?>

    <!-- CARDS -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary p-3">
                <h5>Total</h5>
                <h2><?= $total ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning p-3">
                <h5>Pending</h5>
                <h2><?= $pending ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success p-3">
                <h5>Approved</h5>
                <h2><?= $approved ?></h2>
            </div>
        </div>
    </div>

    <!-- SEARCH -->
    <div style="position: relative; width: 300px;" class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Search name...">
        <div id="suggestions" style="position:absolute;background:white;width:100%;border:1px solid #ccc;display:none;z-index:1000;"></div>
    </div>

    <!-- TABLE -->
    <?php $result = $conn->query("SELECT * FROM appointments ORDER BY id DESC"); ?>

    <div class="card p-3 shadow">
        <h5>Appointment List</h5>

        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['service'] ?></td>
                <td><?= $row['appointment_date'] ?></td>
                <td>
                    <span class="badge bg-<?=
                        $row['status']=='Approved' ? 'success' :
                        ($row['status']=='Cancelled' ? 'danger' : 'warning') ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm">✔</a>
                    <a href="?cancel=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✖</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this appointment permanently?')">
                       <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- CHART NOW LAST -->
    <div class="card p-4 mt-4 shadow">
        <h5 class="text-center">Appointment Statistics</h5>
        <canvas id="myChart"></canvas>
    </div>

<?php endif; ?>

</div>

<!-- AUTO HIDE MESSAGE -->
<script>
setTimeout(() => {
    let msg = document.getElementById("successMsg");
    if (msg) {
        msg.style.transition = "0.5s";
        msg.style.opacity = "0";
        setTimeout(() => msg.remove(), 500);
    }
}, 3000);
</script>

<!-- CHART -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('myChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total', 'Pending', 'Approved'],
            datasets: [{
                label: 'Appointments',
                data: [<?= $total ?>, <?= $pending ?>, <?= $approved ?>],
                borderWidth: 1
            }]
        }
    });
}
</script>

<!-- AUTOCOMPLETE -->
<script>
const searchInput = document.getElementById("search");

if (searchInput) {
    searchInput.addEventListener("keyup", function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr");
        let box = document.getElementById("suggestions");

        box.innerHTML = "";
        let list = [];

        rows.forEach(row => {
            let name = row.children[0].innerText;

            if (name.toLowerCase().includes(value) && value !== "") {
                row.style.display = "";
                if (!list.includes(name)) list.push(name);
            } else {
                row.style.display = "none";
            }
        });

        if (list.length > 0) {
            box.style.display = "block";
            list.forEach(name => {
                let div = document.createElement("div");
                div.innerText = name;
                div.style.padding = "8px";

                div.onclick = () => {
                    searchInput.value = name;
                    box.style.display = "none";
                };

                box.appendChild(div);
            });
        } else {
            box.style.display = "none";
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
