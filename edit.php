<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    die("Employee ID not specified.");
}

$empid = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM employee WHERE empid = $empid");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Employee not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empid = $_POST['empid'];
    $empname = $_POST['empname'];
    $deptid = $_POST['deptid'];
    $emp_mobile = $_POST['emp_mobile'];
    $emp_salary = $_POST['emp_salary'];
    $emp_status = $_POST['emp_status'];
    $emp_start = $_POST['emp_start'];
    $emp_endson = $_POST['emp_endson'];
    $filename = $_POST['old_picture'];

    if (!empty($_FILES['emp_picture']['tmp_name'])) {
        $filename = $empid . ".jpg";
        $destination = "uploads/" . $filename;
        move_uploaded_file($_FILES['emp_picture']['tmp_name'], $destination);
    }

    $sql = "UPDATE employee SET 
                empname = '$empname',
                deptid = '$deptid',
                emp_mobile = '$emp_mobile',
                emp_salary = '$emp_salary',
                emp_status = '$emp_status',
                emp_picture = '$filename',
                emp_start = '$emp_start',
                emp_endson = '$emp_endson'
            WHERE empid = $empid";

    if (mysqli_query($conn, $sql)) {
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
    <title>Edit Employee</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { background: #fff; padding: 20px; max-width: 600px; margin: auto; box-shadow: 0 0 10px #ccc; border-radius: 10px; }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; background: #28a745; color: white; padding: 10px; border: none; border-radius: 4px; width: 100%; cursor: pointer; }
        button:hover { background: #218838; }
        img { margin-top: 10px; border-radius: 4px; display: block; }
        a { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <a href="list.php">← Back to List</a>
    <h2>Edit Employee</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="empid" value="<?php echo $row['empid']; ?>">
        <input type="hidden" name="old_picture" value="<?php echo $row['emp_picture']; ?>">

        <label>Name:</label>
        <input type="text" name="empname" value="<?php echo $row['empname']; ?>" required>

        <label>Department:</label>
        <select name="deptid" required>
            <option value="">--Select--</option>
            <?php
            $depts = mysqli_query($conn, "SELECT * FROM department");
            while ($dept = mysqli_fetch_assoc($depts)) {
                $sel = $dept['deptid'] == $row['deptid'] ? "selected" : "";
                echo "<option value='{$dept['deptid']}' $sel>{$dept['deptname']}</option>";
            }
            ?>
        </select>

        <label>Mobile:</label>
        <input type="text" name="emp_mobile" value="<?php echo $row['emp_mobile']; ?>" required>

        <label>Salary:</label>
        <input type="text" name="emp_salary" value="<?php echo $row['emp_salary']; ?>" required>

        <label>Status:</label>
        <select name="emp_status">
            <option value="1" <?php echo $row['emp_status'] == '1' ? 'selected' : ''; ?>>Active</option>
            <option value="0" <?php echo $row['emp_status'] == '0' ? 'selected' : ''; ?>>Inactive</option>
        </select>

        <label>Start Date:</label>
        <input type="date" name="emp_start" value="<?php echo $row['emp_start']; ?>">

        <label>End Date:</label>
        <input type="date" name="emp_endson" value="<?php echo $row['emp_endson']; ?>">

        <label>Picture:</label>
        <?php if (!empty($row['emp_picture']) && file_exists("uploads/" . $row['emp_picture'])): ?>
            <img id="preview-image" src="uploads/<?php echo $row['emp_picture']; ?>?v=<?php echo time(); ?>" width="80">
        <?php else: ?>
            <img id="preview-image" src="placeholder.png" width="80">
        <?php endif; ?>
        <input type="file" name="emp_picture" accept="image/*">

        <button type="submit">Update</button>
    </form>
</div>

<script>
    document.querySelector('input[name="emp_picture"]').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const imgPreview = document.getElementById('preview-image');
                if (imgPreview) {
                    imgPreview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>
