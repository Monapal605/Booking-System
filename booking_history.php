<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get total bookings
$total_sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_sql);
$total_data = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_data['total'] / $limit);

// Fetch bookings for the page
$bookings_sql = "SELECT * FROM bookings WHERE user_id = $user_id ORDER BY booked_at DESC LIMIT $offset, $limit";
$bookings_result = mysqli_query($conn, $bookings_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Booking History</h2>
            <div>
                <a href="event.php" class="btn btn-secondary">Back to Events</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if (mysqli_num_rows($bookings_result) == 0): ?>
            <div class="alert alert-info">No bookings found.</div>
        <?php else: ?>
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Venue</th>
                        <th>Booked At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                        <?php
                        $event_id = $booking['event_id'];
                        $event_result = mysqli_query($conn, "SELECT name, event_date, venue FROM events WHERE id = $event_id");
                        $event = mysqli_fetch_assoc($event_result);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($event['name']) ?></td>
                            <td><?= $event['event_date'] ?></td>
                            <td><?= htmlspecialchars($event['venue']) ?></td>
                            <td><?= $booking['booked_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</body>
</html>
