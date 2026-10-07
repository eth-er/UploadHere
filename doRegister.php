<?php
// ur mission is after create connection to database, you need to implement the following functionalities in this file:
// 1. Create a POST request to handle registration form submission
// 2. Validate the input fields: username, email, password, confirm_password make sure confirm_password matches password
// 3. Check if the username or email already exists in the database
// 4. If validation passes, hash the password and insert the new user into the database
// 5. If registration is successful, redirect to login.php with a success message

session_start();
require_once 'db.php';
//intinya tuh kaya minta php untuk ambil or jalanin isi dari file db.php ini 

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($username === ''){
    header("Location: register.php?error=" . urlencode("Username must be filled"));
    exit();
}

if (!str_ends_with($email, "@gmail.com")){
    header("Location: register.php?error=" . urlencode("Email must ends with @gmail.com"));
    exit();
}

//check kl username or email udh ada or blm di database
$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
// ? -> placeholder
$statement = $connection->prepare($sql);
//intinya suruh untuk pake koneksi database ini untuk prepare SQL yg ada di $sql 
//jd hasilnya bklan disimpen di $statement
$statement -> bind_param("ss", $username, $email);
//bind param -> untuk gantiin si place holder yg kita taro di $sql dgn $usrname and $email
// ss -> artinya ada 2 parameter and both itu string  
$statement->execute(); //jalanin querynya ke database
$result = $statement->get_result();
//ngambil hasil query yg td, kl usn udh ada brrti 1 row ditemukan kl ga ada brrti 0

if($result -> num_rows > 0){
    //num_rows -> brp banyak row yg ditemukan 
    while ($existingUser = $result->fetch_assoc()){
    //buat tau apa yg udh ada email or username
    //jd fungsi si fetch_assoc itu basically data 1 row td kan bklan diambil tp bentuknya msh dalam tabel nah mkknya pake si fetch_assoc ini buat ubah si datanya jd array

        if ($existingUser['username'] === $username){
        //bandingin yg di database dan inputan
            header("Location: register.php?error=" . urlencode("Username already exists"));
            exit();
        }

        if($existingUser['email'] === $email){
            header("Location: register.php?error=" . urlencode("Email already exists"));
            exit();
        }
    }
}

if (strlen($password) < 8){
    header("Location: register.php?error=" . urlencode("Password must contains at least 8 characters"));
    exit();
}

if($password !== $confirm_password){
    header("Location: register.php?error=" . urlencode("Password and confirm password must match"));
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//passwordnya bklan dihash dgn algoritma yg ditentukan sm PHP

$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$statement = $connection->prepare($sql);
$statement->bind_param("sss", $username, $email, $hashedPassword);

try {
    if ($statement->execute()){
        header("Location: login.php?success=" . urlencode("Registration successful"));
        exit();
    }
} catch (mysqli_sql_exception $e) {
    
}

header("Location: register.php?error=" . urlencode("Registration failed, please try again"));
exit();

?>