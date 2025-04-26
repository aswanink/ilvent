<?php
require_once ("idlinkdependencies/idealnkconf_settings.php");
$obj_employee = new idealink("employee");

// print by using join query and foreach

$arrData = $obj_employee->joinQuery("select empid, empname,emp_mobile,emp_salary from employee where emp_status = '1' order by empid desc");

// print by using getdata and foreach

// $arrData = $obj_employee->joinQuery("select empid, empname, emp_salary from employee where emp_status = '1' order by empid desc");


// print_r($arrData);



echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Employee Name</th><th>employee mobile</th><th>Employee Salary</th><th>Action</th></tr>";


foreach($arrData as $row){

   
echo "<tr>";
echo "<td>" . ($row['empname']) . "</td>";
echo "<td>" . ($row['emp_mobile']) . "</td>";
echo "<td>" . ($row['emp_salary']) . "</td>";
echo "<td>
    <a class='btn' href='insert.php?id=" . $row['empid'] . "'>Edit</a> |
    <a class='btn' href='act_insert.php?id=" . $row['empid'] . "&action=3'>Delete</a>
</td>";



}

echo "</table>";
?>


