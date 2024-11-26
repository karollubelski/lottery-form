<?php
$servername = "sql212.infinityfree.com"; 
$username = "if0_37772778";        
$password = "AFrLboEKG1y0Zhc";    
$database = "if0_37772778_lottery"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
