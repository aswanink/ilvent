<?php
require_once("idlinkdependencies/idealnkconf_settings.php"); 
error_reporting(E_ALL); 
ini_set('display_errors', 0); 
$obj_employee = new idealink("employee"); 
$obj_department = new idealink("department"); 
$noderqstArr = $obj_employee->validateUserInput($_REQUEST); 

// Image deletion in update page 
if (isset($_GET['delete_image']) && $_GET['delete_image'] == 1 && !empty($_GET['id'])) {
    $empid = $_GET['id']; 
    $imageData = $obj_employee->joinQueryRow("SELECT emp_picture FROM employee WHERE empid = '$empid'"); 
    if (!empty($imageData['emp_picture'])) {
        $imagePath = DIR_ROOT . "employ/" . $imageData['emp_picture']; 
        if (file_exists($imagePath)) {
            unlink($imagePath); 
            $obj_employee->update(['emp_picture' => ''], "empid = '$empid'"); 
            header("Location: add.php?id=$empid&msg=img_deleted"); 
            exit();
        } else {
            header("Location: add.php?id=$empid&msg=img_not_found"); 
            exit();
        }
    } else {
        header("Location: add.php?id=$empid&msg=no_img"); 
        exit();
    }
}

$arrData = []; 
if (!empty($noderqstArr['id'])) {
    $empid = $noderqstArr['id']; 
    $arrData = $obj_employee->joinQueryRow("
    SELECT e.*, l.locname 
    FROM employee e 
    LEFT JOIN location l ON l.locid = e.locid 
    WHERE e.empid = '$empid'
");
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Employee</title>
    <style>
        /* Styling for the entire page */
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
        }

        /* Heading styling */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        /* Form container styling */
        .form-container {
            width: 600px;
            max-width: 90%;
            margin: 30px auto;
            padding: 30px 40px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
        }

        /* Label styling */
        label {
            display: block;
            margin-top: 20px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 15px;
            color: #555;
        }

        /* Input and Select field styling */
        input[type="text"],
        input[type="date"],
        select {
            width: 75%;
            padding: 10px 15px;
            margin-top: 10px;
            box-sizing: border-box;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            background-color: #fff;
        }

        /* Focus effect for Input fields */
        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        /* Submit button styling */
        input[type="submit"] {
            width: 50%;
            padding: 12px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Submit button hover effect */
        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Back button styling */
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c63ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Hover effect for back button */
        .btn-back:hover {
            background-color: #554fd1;
        }

        /* Location suggestion dropdown styling */
        #location_suggestions {
            position: absolute;
            background-color: #fff;
            border: 2px solid #4CAF50;
            padding: 10px;
            display: none;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            width: calc(15% - 20px);
            z-index: 50%;
        }

        /* Location suggestion item styling */
        .location-suggestion {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            font-weight: bold;
            color: #333;
        }

        /* Hover effect for location suggestion */
        .location-suggestion:hover {
            background-color: #4CAF50;
            color: #fff;
        }

        .location-suggestion:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<!-- Back Button -->
<a href="list.php" class="btn-back">← Back to Employee List</a>

<h2>ADD EMPLOYEES</h2>

<?php
// Display any status messages passed via the query string
if (!empty($_GET['msg'])) {
    if ($_GET['msg'] == 'img_deleted') echo "<p style='color:green;'></p>";
    elseif ($_GET['msg'] == 'img_not_found') echo "<p style='color:red;'>Image file not found.</p>";
    elseif ($_GET['msg'] == 'no_img') echo "<p style='color:orange;'>No image to delete.</p>";
}
?>

<br><center>

<!-- Form to add or update employee details -->
<div style="border: 2px solid black; width: 600px; padding:2px;">
<form action="act_insert.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="empid" id="empid" value="<?php echo $arrData['empid'] ?? ''; ?>" /> 

    <label>Name</label>
    <input type="text" name="empname" required value="<?php echo $arrData['empname'] ?? ''; ?>" tabindex="1"><br><br>

    <label>Department</label>
    <select name="deptid" id="deptid" tabindex="2" class="form-control" required style="padding: 10px 15px; width: 75%;">
        <option value=''>Select Department</option>
        <?php echo $obj_department->createListBox("deptid", "deptname", $arrData['deptid'] ?? "", "deptname<>''", "deptid desc"); ?>
    </select>

    <label>Subject</label>
    <input type="hidden" id="subid_hidden" value="<?php echo $arrData['subid'] ?? ''; ?>">
    <select name="subid" id="subid" class="form-control" required style="padding: 10px 15px; width: 75%;" tabindex="3">
        <option value="">Select Subject</option>
    </select>

    <label for="location_input">Enter Location</label>
    <input type="text" id="location_input" name="location_input" class="form-control" value="<?php echo $arrData['locname'] ?? ''; ?>"
    autocomplete="off">
    <input type="hidden" id="locid" name="locid" value="1">
    <div id="location_suggestions"></div>

    <label>Mobile</label>
    <input type="tel" name="emp_mobile" id="emp_mobile" required maxlength="10" value="<?php echo $arrData['emp_mobile'] ?? ''; ?>" tabindex="5"><br><br>

    <label>Salary</label>
    <input type="text" name="emp_salary" required value="<?php echo $arrData['emp_salary'] ?? ''; ?>" tabindex="6"><br><br>

    <label>Status</label>
    <select name="emp_status" class="form-control" required style="padding: 10px 15px; width: 75%;" tabindex="7">
        <option value=''>Select Status</option>
        <option value="1" <?php if (($arrData['emp_status'] ?? '') == '1') echo 'selected'; ?>>Active</option>
        <option value="0" <?php if (($arrData['emp_status'] ?? '') == '0') echo 'selected'; ?>>Inactive</option>
    </select><br><br>

    <label>Start date</label>
    <input type="date" name="emp_start" required value="<?php echo $arrData['emp_start'] ?? ''; ?>" tabindex="8"><br><br>

    <label>End date</label>
    <input type="date" name="emp_endson" required value="<?php echo $arrData['emp_endson'] ?? ''; ?>" tabindex="9"><br><br>

    <!-- Employee Picture Upload -->
    <label>Picture:</label><br>
    <?php if (!empty($arrData['emp_picture'])): ?>
        <img src="employ/<?php echo htmlspecialchars($arrData['emp_picture']); ?>" width="80" height="80" style="object-fit: cover; border-radius: 5px;"><br><br>
        <a href="add.php?delete_image=1&id=<?php echo $arrData['empid']; ?>" onclick="return confirm('Are you sure you want to delete this image?')" style="color:red; text-decoration:none;">Delete Image</a><br><br>
    <?php else: ?>
        No image selected<br><br>
    <?php endif; ?>
    <input type="file" name="emp_picture" <?php if (empty($arrData['empid'])) echo 'required'; ?> tabindex="10"><br><br>

    
    <input type="submit" name="btnsave" value="SAVE" tabindex="11">
</form>


</center>
</div>

<!--  Bootstrap and jQuery for interactive elements -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    
    // subject loading if department is already selected
    $(document).ready(function () {
        $('#deptid').on('change', function () {
            var deptid = $(this).val();
            var selectedSubId = $('#subid_hidden').val() || '';
            if (deptid !== '') {
                $.ajax({
                    url: 'phpajax_subject.php',
                    type: 'POST',
                    data: { deptid: deptid, selected: selectedSubId },
                    success: function (response) {
                        $('#subid').html(response);
                    }
                });
            } else {
                $('#subid').html('<option value="">Select Subject</option>');
            }
        });

        
        if ($('#deptid').val() !== '') {
            $('#deptid').trigger('change');
        }

        $('#location_input').on('keyup', function () {
        var location = $(this).val();
        if (location !== '') {
            $.ajax({
                url: 'phpajax_location.php', 
                type: 'POST',
                data: { location: location }, 
                success: function (response) {
                    $('#location_suggestions').html(response);
                    $('#location_suggestions').show();
                }
            });
        } else {
            
            $('#location_suggestions').hide();
        }
    });

    // Handle the selection of a location suggestion
    $(document).on('click', '.location-suggestion', function () {
        var locid = $(this).data('locid'); 
        var location = $(this).text(); 
        $('#locid').val(locid); 
        $('#location_input').val(location); 
        $('#location_suggestions').hide(); 
    });

    // Form submission validation for location selection
    $('form').submit(function (e) {
        if ($('#is_edit').val() == '0' && $('#locid').val() === '') {
            alert('Please select a location from the suggestions');
            e.preventDefault(); // Prevent form submission
        }
    });

    // Form submission validation for mobile number length
        $('form').submit(function (e) {
            var phone = $('#emp_mobile').val();
            if (phone.length !== 10) {
                alert('Please enter a valid 10-digit phone number.');
                e.preventDefault();
                return false;
            }
        });
    });
</script>

</body>
</html>
