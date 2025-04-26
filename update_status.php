<?php
include 'db.php';

if (isset($_POST['empid']) && isset($_POST['status'])) {
    $empid = (int)$_POST['empid'];  
    $status = $_POST['status']; 

   
    error_log("Emp ID: " . $empid . ", Status: " . $status);

   
    $query = "UPDATE employee SET emp_status = '$status' WHERE empid = $empid";

    if (mysqli_query($conn, $query)) {
      
        echo "success";
    } else {
     
        error_log("Error updating status: " . mysqli_error($conn));
        echo "error";
    }
} else {
    error_log("Invalid data received for updating status");
    echo "error";
}

?>
