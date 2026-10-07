<?php
// ur mission is after create connection to database, you need to implement the following functionalities in this file:
// 1. Create a POST request to handle file upload form submission & validate the user session to ensure the user is authenticated before allowing file upload
// 2. Validate the uploaded file to ensure it meets the required criteria (e.g., file type, size limit)
// try to limit the file size to 5MB and only allow certain file types (e.g., PDF, DOCX, JPG, PNG)
// 3. Move the uploaded file to a designated directory on the server (e.g., "uploads/")
// 4. Store the file information (e.g., file name, size, upload date, user ID) in the database for future reference

session_start();
require_once 'db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['user_id'])){ 
    header("Location: login.php?error=" . urlencode("Please login first"));
    exit();
}

if (!isset($_FILES['fileUpload'])){
    header("Location: upload.php?error=" . urlencode("File upload failed"));
    exit();
}

$file = $_FILES['fileUpload'];

if($file['error'] !== UPLOAD_ERR_OK){ //intinya kl upload berhasil
    header("Location: upload.php?error=" . urlencode("File upload failed")); // FIX: ke upload.php, hapus spasi
    exit();
}

$size = 5 * 1024 * 1024;
//1 mb -> 1024 kb
//1 kb -> 1024 bytes

if ($file['size'] > $size){
    header("Location: upload.php?error=" . urlencode("File more than 5MB")); // FIX: typo Location, ke upload.php
    exit();
}

//file yg dibolehin: ekstensi -> mime yg valid buat ekstensi itu
$allowedFileTypes = [
    'pdf'  => ['application/pdf'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png']
];

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!array_key_exists($extension, $allowedFileTypes)){
    header("Location: upload.php?error=" . urlencode("File extension is not allowed"));
    exit();
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$realType = $finfo->file($file['tmp_name']);

if (!in_array($realType, $allowedFileTypes[$extension], true)){
    header("Location: upload.php?error=" . urlencode("File type is not allowed"));
    exit();
}

//move file to uploads

$uploadSpace = "uploads/";
$savedName = bin2hex(random_bytes(16)) . "." . $extension;
$finalPath = $uploadSpace . $savedName;

if (!move_uploaded_file($file['tmp_name'], $finalPath)) {
    header("Location: upload.php?error=" . urlencode("Failed to upload file"));
    exit();
}


//store file information in database

$userId = $_SESSION['user_id'];
$originalName = basename($file['name']); 
$fileSize = $file['size'];
$fileType = $realType; 

$sql = "INSERT INTO files (user_id, original_name, stored_name, file_size, file_type) VALUES (?, ?, ?, ?, ?)";

$statement = $connection->prepare($sql);

$statement->bind_param("issis", $userId, $originalName, $savedName, $fileSize, $fileType);

if ($statement->execute()) {
    header("Location: list.php?success=" . urlencode("File uploaded successfully"));
    exit();
}

unlink($finalPath);
header("Location: upload.php?error=" . urlencode("Failed to save file data"));
exit();

?>
