<?php ob_start();
    require_once ("idlinkdependencies/idealnkconf_settings.php");
    require_once (DIR_ROOT."idlcls/class.upload.php");
    $obj_employee = new idealink("employee");
    $POST         = $obj_employee->validateUserInput ($_POST);
    $REQUESTARR   = $obj_employee->validateUserInput ($_REQUEST);
    $path_icon    = DIR_ROOT.'employ';




//INSERT -INSERT


if(($POST ['btnsave']=="SAVE") && ($POST['empid'] == '')) {
    $InsertArray = array(
        'empname'       => $POST['empname'],
        'deptid'        => $POST['deptid'],
        'emp_mobile'    => $POST['emp_mobile'],
        'emp_salary'    => $POST['emp_salary'],
        'emp_picture'   => $POST['emp_picture'], 
        'emp_start'     => $POST['emp_start'],
        'emp_endson'    => $POST['emp_endson'],
        'emp_status'    => $POST['emp_status'] ?? 1,
        'subid'         => $POST['subid'],
        'locid'         => $POST['locid']
        
        
    );
    

//IMAGE UPLOAD

    if ($obj_employee->insert($InsertArray)) {
        $emp_id = $obj_employee->insertId();
        if ($_FILES['emp_picture']['size'] > 0) {
            $file = new Upload($_FILES['emp_picture']);
            $file->file_new_name_body = 'employ_'.$emp_id;

            if ($file->uploaded) {
                $file->Process($path_icon);

                if ($file->processed) {
                    $updateArray = array('emp_picture' => $file->file_dst_name);
                    $obj_employee->update($updateArray, "empid = '$emp_id'");
                }
            }
        }

        header("Location: list.php");
    }
}

//  EDIT

if (isset($POST['btnsave']) && $POST['btnsave'] == "SAVE" && !empty($POST['empid'])) {
    $empid = $POST['empid'];

    $updateArray = array(
        'empname'     => $POST['empname'],
        'deptid'      => $POST['deptid'],
        'emp_mobile'  => $POST['emp_mobile'],
        'emp_salary'  => $POST['emp_salary'],
        'emp_start'   => $POST['emp_start'],
        'emp_endson'  => $POST['emp_endson'],
        'emp_status'  => $POST['emp_status'],
        'subid'       => $POST['subid'],
        'locid'       => $POST['locid']
        
    );

    // Handle new image upload

    if ($_FILES['emp_picture']['size'] > 0) {
        $oldData = $obj_employee->joinQueryRow("SELECT emp_picture FROM employee WHERE empid = '$empid'");
        $oldImage = $oldData['emp_picture'];
        $file = new Upload($_FILES['emp_picture']);
        $file->file_new_name_body = 'employ_' . $empid;

        if ($file->uploaded) {
            $file->Process($path_icon);
            if ($file->processed) {
                $updateArray['emp_picture'] = $file->file_dst_name;
            }
        }
    }

    $obj_employee->update($updateArray, "empid='$empid'");
    header("Location: list.php");
    exit();
}

// POPUP

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'toggle_status') {
    $empid = $_POST['empid'];
    $current_status = $_POST['current_status'];
    $new_status = ($current_status == 1) ? 0 : 1;
    $obj_employee->update(['emp_status' => $new_status], "empid = '$empid'");
    echo "Status updated";
    exit;
}
    
//DELETE

    if (($REQUESTARR['action'] == "3") && ($REQUESTARR['id'] <> "")) {
    $empid = $REQUESTARR['id'];
    $obj_employee->delete("empid = '$empid'");
    header("location:list.php");
   
}

