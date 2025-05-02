<?php

$servername = 'localhost';
$username = 'root';
$password = '_password';
$db_name = 'todolistapp';

$conn = mysqli_connect($servername, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

