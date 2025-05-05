<?php

$servername = 'localhost';
$username = 'root';
$password = '1234';
$db_name = 'todolistapp';

$conn = mysqli_connect($servername, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

