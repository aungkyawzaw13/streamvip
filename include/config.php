<?php
$conn = new mysqli("127.0.0.1","root","","movie");
if($conn->connect_error){
    die(json_encode(["status"=>"error","msg"=>"Database connection failed: ".$conn->connect_error]));
}
