<?php
include 'includes/logger.php';
$start = microtime(true);
?>
<?php
include 'includes/db.php';

$msg="";

if(isset($_POST['register'])){
$name=$_POST['name'];
$email=$_POST['email'];
$pass=password_hash($_POST['password'],PASSWORD_DEFAULT);

$conn->query("INSERT INTO users(name,email,password) VALUES('$name','$email','$pass')");
$msg="Registered Successfully ✅";
}
?>

<link rel="stylesheet" href="css/style.css">

<div class="auth-bg">
<div class="auth-box">

<h2>📝 Register</h2>
<p class="auth-msg"><?php echo $msg; ?></p>

<form method="POST">
<input type="text" name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button name="register">Register</button>
</form>

<p style="color:white;">
Already have account? <a href="login.php">Login</a>
</p>

</div>
</div>
<?php
$status = 200;
$latency = round((microtime(true) - $start) * 1000);

logApi("/register.php", $status, $latency);
?>