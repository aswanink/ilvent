<?php 
ob_start();
require_once("idlinkdependencies/idealnkconf_settings.php");

$obj_subject = new idealink("subject_master");
$obj_employee_mast = new idealink("employee");


$POST = $obj_employee_mast->validateUserInput($_POST);


if (isset($POST['deptid']) && !empty($POST['deptid'])) {
    $deptid = $POST['deptid'];
    $selected = isset($POST['selected']) ? $POST['selected'] : '';

    
    echo $obj_subject->createListBox("subid", "subname", $selected, "deptid = '$deptid'", "subname ASC");
} else {
    echo "<option value=''>Invalid request</option>";
}



?>
