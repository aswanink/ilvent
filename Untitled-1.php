<?php 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empname = $_POST['empname'];
    $deptid = $_POST['deptid'];
    $emp_mobile = $_POST['emp_mobile'];
    $emp_salary = $_POST['emp_salary'];
    $emp_status = $_POST['emp_status'];
    $emp_start = $_POST['emp_start'];
    $emp_endson = $_POST['emp_endson'];
    $created_date = date('Y-m-d H:i:s');


    $sql = "INSERT INTO employee (empname, deptid, emp_mobile, emp_salary, emp_status, emp_start, emp_endson, created_date)
            VALUES ('$empname', '$deptid', '$emp_mobile', '$emp_salary', '$emp_status', '$emp_start', '$emp_endson', '$created_date')";

    if (mysqli_query($conn, $sql)) {
       
        $empid = mysqli_insert_id($conn);

     
        if (!empty($_FILES['emp_picture']['tmp_name'])) {
            $imagePath = "uploads/" . $empid . ".jpg";
            move_uploaded_file($_FILES['emp_picture']['tmp_name'], $imagePath);

          
            mysqli_query($conn, "UPDATE employee SET emp_picture = '$empid.jpg' WHERE empid = $empid");
        }

     
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

 ?>
 

<!DOCTYPE html>
<html>
<head>


    <title>Add Employee</title>

    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background-color: #f7f7f7;
    }

    h2 {
        margin-bottom: 20px;
    }

    a {
        text-decoration: none;
        background-color: #4CAF50;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        margin-bottom: 20px;
        display: inline-block;
    }

    form {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="date"],
    select,
    input[type="file"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    button {
        margin-top: 20px;
        padding: 10px 15px;
        background-color: #2196F3;
        border: none;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0b7dda;
    }
</style>
</head>
<body>
    <center>
<a href="list.php">← Back to Employee List</a>

<h2>Add Employee</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Name:</label><br>
    <input type="text" name="empname" required><br><br>

    <label>Department:</label><br>
    <select name="deptid" required>
        <option value="">--Select--</option>
        <?php
        $deptResult = mysqli_query($conn, "SELECT * FROM department");
        while ($dept = mysqli_fetch_assoc($deptResult)) {
            echo "<option value='" . $dept['deptid'] . "'>" . $dept['deptname'] . "</option>";
        }
        ?>
    </select><br><br>

    <label>Mobile:</label><br>
    <input type="text" name="emp_mobile" required><br><br>

    <label>Salary:</label><br>
    <input type="text" name="emp_salary" required><br><br>

    <label>Status:</label><br>
    <select name="emp_status">
        <option value="">--Select--</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select><br><br>

    <label>Start Date:</label><br>
    <input type="date" name="emp_start"><br><br>

    <label>End Date:</label><br>
    <input type="date" name="emp_endson"><br><br>

    <label>Picture:</label><br>
    <input type="file" name="emp_picture" required><br><br>

    <button type="submit">Add Employee</button>
</form>
    </center>
</body>
</html>
