<?php
session_start();
header('Content-Type: application/json');

$verified = isset($_SESSION['email_verified']) && $_SESSION['email_verified'] === true;

echo json_encode(['verified' => $verified]);
?>