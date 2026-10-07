<?php
// ur mission is after create connection to database, you need to implement the following functionalities in this file:
// 1. Create a POST request to handle login form submission
// 2. Validate the username and password against the database
// 3. If the credentials are valid, start a session and redirect to index.php
// 4. If the credentials are invalid, redirect back to login.php with an error message
// 5. Dont forget to include session_start() at the beginning of the file to manage user sessions

session_start();
require_once 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$statement = $connection->prepare($sql);
$statement->bind_param("s", $username);
$statement->execute();
$result = $statement->get_result();

if($result -> num_rows > 0){
    $existingUser = $result -> fetch_assoc();

    if(password_verify($password, $existingUser['password'])){
        $_SESSION['user_id'] = $existingUser['id'];
        $_SESSION['username'] = $username; 
        header("Location: index.php");
        exit();
    } else{
        header("Location: login.php?error=" . urlencode("Username or password are not match"));
        exit();
    }
} else{
    header("Location: login.php?error=" . urlencode("Username or password are not match"));
    exit();
}
?>