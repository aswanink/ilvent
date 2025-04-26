<?php
include 'db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM employee WHERE empid = $id");
header("Location: list.php");
