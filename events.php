<?php
include 'includes/logger.php';

$start = microtime(true);
?>
<?php
include 'includes/db.php';
include 'includes/header.php';

// RUN QUERY FIRST (IMPORTANT 🔥)
$res = $conn->query("SELECT * FROM events");

// CHECK QUERY ERROR
if(!$res){
    die("Query Failed: " . $conn->error);
}
?>

<div class="grid">

<?php
while($row = $res->fetch_assoc()){
?>

<div class="card">
    <h3><?php echo $row['title']; ?></h3>
    <p><?php echo $row['description']; ?></p>
    <p><b>Date:</b> <?php echo $row['date']; ?></p>
    <p><b>Location:</b> <?php echo $row['location']; ?></p>
    <p class="price">₹<?php echo $row['price']; ?></p>

    <a class="btn" href="book.php?id=<?php echo $row['id']; ?>">Book Now</a>
</div>

<?php
}
?>

</div>

<?php include 'includes/footer.php'; ?>
<?php

$status = 200;

$latency = round((microtime(true) - $start) * 1000);

logApi("/events.php", $status, $latency);

?>
<?php

include 'includes/logger.php';

$start = microtime(true);

sleep(3);

/* existing code */

$status = 200;

$latency = round((microtime(true) - $start) * 1000);

logApi("/events.php", $status, $latency);

?>
sleep(2);