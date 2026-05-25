<?php
session_start();

include 'includes/logger.php';
include 'includes/db.php';
include 'includes/header.php';

$start = microtime(true);

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

if(!isset($_SESSION['payment'])){
    $latency = round((microtime(true) - $start) * 1000);
    logApi("/payment.php", 404, $latency, "No payment data found");

    echo "<p style='text-align:center;'>No payment data found</p>";
    include 'includes/footer.php';
    exit();
}

$user_id = $_SESSION['user']['id'];
$event_id = $_SESSION['payment']['event_id'];
$amount = $_SESSION['payment']['amount'];

if(isset($_GET['timeout'])){
    sleep(2);

    $latency = round((microtime(true) - $start) * 1000);
    logApi("/payment.php", 504, $latency, "Payment gateway timeout");

    echo "
    <div style='text-align:center;margin-top:100px;color:red;font-family:Arial;'>
        <h1>❌ Payment Failed</h1>
        <h2>Gateway Timeout</h2>
        <p>User did not complete payment within 10 seconds.</p>
    </div>
    ";

    include 'includes/footer.php';
    exit();
}

if(isset($_POST['pay'])){
    echo "
    <div style='
        height:100vh;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        background:#061b16;
        color:white;
        font-family:Arial;
    '>

        <h1>💳 Processing Payment...</h1>

        <div style='
            width:80px;
            height:80px;
            border:8px solid #1f4d45;
            border-top:8px solid #00ff99;
            border-radius:50%;
            animation:spin 1s linear infinite;
            margin:30px;
        '></div>

        <h2 id='timer'>10 sec</h2>

        <p>Please complete payment before timer ends...</p>

    </div>

    <style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>

    <script>
    let time = 10;

    let timer = setInterval(() => {
        time--;
        document.getElementById('timer').innerHTML = time + ' sec';

        if(time <= 0){
            clearInterval(timer);
            window.location = 'payment.php?timeout=true';
        }
    }, 1000);
    </script>
    ";

    exit();
}
?>

<div class="payment-box">
    <h2>💳 Payment Gateway</h2>

    <p><b>Total Amount:</b> ₹<?php echo $amount; ?></p>

    <form method="POST">
        <div class="payment-option">
            <input type="radio" name="method" value="UPI" required> UPI
        </div>

        <div class="payment-option">
            <input type="radio" name="method" value="Card"> Credit / Debit Card
        </div>

        <div class="payment-option">
            <input type="radio" name="method" value="NetBanking"> Net Banking
        </div>

        <input type="text" placeholder="Enter UPI ID / Card Number" required>

        <button name="pay">Pay Now 💸</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>