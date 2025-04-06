<?php
session_start();
if (!isset($_SESSION['email_temp'])) {
    echo "No email provided for OTP verification.";
    exit();
}

$email = $_SESSION['user_email']; // Retrieve the email from the session

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body>

<h2>OTP Verification</h2>
<p>An OTP has been sent to your email. Please enter the OTP below to verify your email.</p>

<form action="verify_opt.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $_SESSION['email_temp']; ?>" readonly><br><br>

    <label for="otp">OTP:</label>
    <input type="text" name="otp" required><br><br>

    <button type="submit">Verify OTP</button>
</form>

</body>
</html>
