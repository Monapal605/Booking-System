<?php
session_start();
include 'connection.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['user_id'];

    // Check event seat availability
    $event_res = mysqli_query($conn, "SELECT available_seats FROM events WHERE id = $event_id");
    if ($event_res && mysqli_num_rows($event_res) > 0) {
        $event = mysqli_fetch_assoc($event_res);
        if ($event['available_seats'] > 0) {
            // Insert booking
            $insert_booking = mysqli_query($conn, "INSERT INTO bookings (user_id, event_id) VALUES ($user_id, $event_id)");
            if ($insert_booking) {
                // Decrement seat
                mysqli_query($conn, "UPDATE events SET available_seats = available_seats - 1 WHERE id = $event_id");
                $success = "Ticket booked successfully!";
            } else {
                $error = "Booking failed.";
            }
        } else {
            $error = "No seats available for this event.";
        }
    } else {
        $error = "Event not found.";
    }
}

// Fetch all events
$events = mysqli_query($conn, "SELECT * FROM events");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-4">Available Events</h2>
            <div>
                <a href="booking_history.php" class="btn btn-secondary">My Bookings</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <?php while ($event = mysqli_fetch_assoc($events)): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>
                            <p class="card-text">
                                <strong>Date:</strong> <?= $event['event_date'] ?><br>
                                <strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?><br>
                                <strong>Seats Available:</strong> <?= $event['available_seats'] ?>
                            </p>
                            <form method="POST">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn-primary" <?= $event['available_seats'] == 0 ? 'disabled' : '' ?>>
                                    Book Ticket
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
