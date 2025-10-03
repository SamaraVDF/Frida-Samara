<?php
$database="en_linea";
$user="root";
$password="";
$host="localhost";

$conn=mysqli_connect($host,$user,$password,$database);

if($conn->connect_error){
    die("Conexion fallo ").$conn->connect_error;
}