<?php
include 'includes/logger.php';

$start = microtime(true);
?>
<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Check login
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// Get event ID
if(!isset($_GET['id'])){
    echo "<p>Invalid Event</p>";
    exit();
}

$id = $_GET['id'];

// Fetch event details
$res = $conn->query("SELECT * FROM events WHERE id=$id");
$event = $res->fetch_assoc();

if(!$event){
    echo "<p>Event not found</p>";
    exit();
}

// Handle booking
if(isset($_POST['book'])){
    $tickets = $_POST['tickets'];
    $user_id = $_SESSION['user']['id'];

    // Insert booking
    $conn->query("INSERT INTO bookings(user_id,event_id,tickets) VALUES($user_id,$id,$tickets)");

    // Store for payment
    $_SESSION['payment'] = [
        'event_id' => $id,
        'amount' => $tickets * $event['price']
    ];

    // Redirect to payment
    header("Location: payment.php");
    exit();
}
?>

<div class="card">
    <h2><?php echo $event['title']; ?></h2>
    <p><?php echo $event['description']; ?></p>
    <p><b>Date:</b> <?php echo $event['date']; ?></p>
    <p><b>Location:</b> <?php echo $event['location']; ?></p>
    <p><b>Price per ticket:</b> ₹<?php echo $event['price']; ?></p>
</div>

<div class="form-box">
    <h3>🎟️ Book Tickets</h3>

    <form method="POST">
        <input type="number" name="tickets" placeholder="Enter number of tickets" min="1" required>

        <button name="book">Proceed to Payment 💳</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
<?php

$status = 200;

$latency = round((microtime(true) - $start) * 1000);

logApi("/book.php", $status, $latency);

?>
<?php
$status = 200;
$latency = round((microtime(true) - $start) * 1000);

logApi("/book.php", $status, $latency);
?>