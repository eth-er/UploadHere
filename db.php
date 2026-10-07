<?php

//ini untuk connection dari php ke sqlnya
$connection = new mysqli( //jalur or koneksi dari php ke database
    "localhost", //localhost krn mysqlnya di our laptop
    "root", //username untuk login ke mysql 
    "", //password mysql
    "db_upload_exercise" //database yg mau digunakan 
); 

if ($connection -> connect_error){ //intinya kl koneksi database punya error
    die("connection failed: " . $connection->connect_error);
    //die -> hentiin program phpnya
}

