<?php
// save_phrase.php

$host = "localhost";
$user = "root";
$pass = "";
$db = "pronalysis";
$charset = "utf8mb4";

$con = mysqli_connect($host, $user, $pass, $db) or die("Couldn't connect");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

$data = json_decode(file_get_contents("php://input"));

$phrase = $data->phrase;
$classification = $data->classification;

$stmt = $con->prepare("INSERT INTO phrases (phrase, classification) VALUES (?, ?)");
$stmt->bind_param("ss", $phrase, $classification);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$con->close();
?>
