<?php
require_once("idlinkdependencies/idealnkconf_settings.php");
$obj_employee = new idealink("employee");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $empid = $_GET['id'];

    $emp = $obj_employee->joinQueryRow("
        SELECT e.*, d.deptname, s.subname 
        FROM employee e 
        LEFT JOIN department d ON e.deptid = d.deptid 
        LEFT JOIN subject_master s ON e.subid = s.subid 
        WHERE e.empid = '$empid'
    ");

    if ($emp) {
        echo "<strong>Name:</strong> " . htmlspecialchars($emp['empname']) . "<br>";
        echo "<strong>Department:</strong> " . htmlspecialchars($emp['deptname']) . "<br>";
        echo "<strong>Subject:</strong> " . htmlspecialchars($emp['subname']) . "<br>";
        echo "<strong>Mobile:</strong> " . htmlspecialchars($emp['emp_mobile']) . "<br>";
        echo "<strong>Salary:</strong> " . htmlspecialchars($emp['emp_salary']) . "<br>";
        echo "<strong>Status:</strong> " . ($emp['emp_status'] == 1 ? "Active" : "Inactive") . "<br>";
        echo "<strong>Start Date:</strong> " . htmlspecialchars($emp['emp_start']) . "<br>";
        echo "<strong>End Date:</strong> " . htmlspecialchars($emp['emp_endson']) . "<br>";
        if (!empty($emp['emp_picture'])) {
            echo "<img src='employ/" . htmlspecialchars($emp['emp_picture']) . "' width='100' style='margin-top:10px;'>";
        } else {
            echo "No image available.";
        }
    } else {
        echo "Employee not found.";
    }
}
?>
