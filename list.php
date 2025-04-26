<?php
require_once ("idlinkdependencies/idealnkconf_settings.php");
$obj_employee = new idealink("employee");

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
// SEARCH & FILTER
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedDept = isset($_GET['dept']) ? $_GET['dept'] : '';  
$filterSql = [];


// Sorting logic
$sortBy = isset($_GET['sortby']) ? $_GET['sortby'] : '';
$orderBy = "ORDER BY e.empid DESC"; 

switch ($sortBy) {
    case 'name_asc':
        $orderBy = "ORDER BY e.empname ASC";
        break;
    case 'name_desc':
        $orderBy = "ORDER BY e.empname DESC";
        break;
    case 'salary_asc':
        $orderBy = "ORDER BY e.emp_salary ASC";
        break;
    case 'salary_desc':
        $orderBy = "ORDER BY e.emp_salary DESC";
        break;
}

if (!empty($search)) {
    $search = mysqli_real_escape_string($obj_employee->getDbConnection(), $search);
    $filterSql[] = "(e.empname LIKE '$search%' OR e.emp_mobile LIKE '$search%')";
}

if (!empty($selectedDept)) {
    $selectedDept = (int)$selectedDept;
    $filterSql[] = "e.deptid = $selectedDept";
}
$whereClause = !empty($filterSql) ? 'WHERE ' . implode(' AND ', $filterSql) : '';
$sqlCount = "
SELECT COUNT(*) AS total 
FROM employee e 
LEFT JOIN department d ON d.deptid = e.deptid 
LEFT JOIN subject_master s ON s.subid = e.subid
$whereClause
";
$totalData = $obj_employee->joinQuery($sqlCount);
$totalPages = ceil($totalData[0]['total'] / $limit);
$arrData = $obj_employee->joinQuery("
 SELECT 
     e.empid, 
     e.empname, 
     d.deptname,
     s.subname,
     l.locname,
     e.emp_mobile, 
     e.emp_salary,
     e.emp_picture, 
     e.emp_status,
     DATE_FORMAT(e.emp_start, '%d/%m/%Y') as emp_start, 
     DATE_FORMAT(e.emp_endson, '%d/%m/%Y') as emp_endson 
 FROM employee e
 LEFT JOIN department d ON d.deptid = e.deptid
 LEFT JOIN subject_master s ON s.subid = e.subid
 LEFT JOIN location l ON l.locid = e.locid 
 $whereClause
 $orderBy
 LIMIT $offset, $limit
 ");
?>


<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Employee List</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #333; color: white; }
        a.btn, .btn-add { background: #007bff; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; }
        a.btn:hover, .btn-add:hover { background: #0056b3; }
        .btn-add { background: #28a745; margin-bottom: 15px; display: inline-block; }
        .status-toggle { padding: 6px 12px; border-radius: 4px; font-weight: bold; color: #fff; text-decoration: none; }
        .status-active { background-color: green; }
        .status-inactive { background-color: red; }
        .pagination a { margin: 0 5px; padding: 5px 10px; text-decoration: none; background: #ddd; border-radius: 4px; }
        .pagination a.active { background: #007bff; color: white; }
    </style>
</head>


<body>
<h2>Employee List</h2>

<a href="add.php" class="btn-add">+ Add New Employee</a>

<?php

// Get department list
$departments = $obj_employee->joinQuery("SELECT deptid, deptname FROM department ORDER BY deptname ASC");
$selectedDept = isset($_GET['dept']) ? $_GET['dept'] : '';
?>

<!-- SEARCH -->

<form method="GET" class="mb-3">

    <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search by Name or Mobile" class="form-control w-25 d-inline-block me-2">
    
    <select name="dept" class="form-control w-25 d-inline-block me-2">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?php echo $dept['deptid']; ?>" <?php echo ($selectedDept == $dept['deptid']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($dept['deptname']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="sortby" class="form-control w-25 d-inline-block me-2">
        <option value="">Sort By</option>
        <option value="name_asc" <?php echo ($sortBy == 'name_asc') ? 'selected' : ''; ?>>Name A-Z</option>
        <option value="name_desc" <?php echo ($sortBy == 'name_desc') ? 'selected' : ''; ?>>Name Z-A</option>
        <option value="salary_asc" <?php echo ($sortBy == 'salary_asc') ? 'selected' : ''; ?>>Salary Low to High</option>
        <option value="salary_desc" <?php echo ($sortBy == 'salary_desc') ? 'selected' : ''; ?>>Salary High to Low</option>
    </select>

    <button type="submit" class="btn btn-primary">Search</button>
</form>

<table border='1' cellpadding='5' cellspacing='0'>
    <tr>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Subject</th>
        <th>Location</th>
        <th>Mobile</th>
        <th>Salary</th>
        <th>Picture</th>
        <th>Status</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Action</th>
    </tr>

    <?php foreach ($arrData as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['empname']); ?></td>
            <td><?php echo htmlspecialchars($row['deptname']); ?></td>
            <td><?php echo htmlspecialchars($row['subname']); ?></td>
            <td><?php echo htmlspecialchars($row['locname'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['emp_mobile']); ?></td>
                <?php
                    $empObj = new EmployeeFormatted($row['emp_salary']);
                ?>
            <td><?php echo '₹' . $empObj->getFormattedSalary(); ?></td>

            <td>
                <?php if (!empty($row['emp_picture'])): ?>
                    <img src="employ/<?php echo htmlspecialchars($row['emp_picture']); ?>" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
                <?php else: ?>
                    <span>No Image</span>
                <?php endif; ?>
            </td>

            <td>
                <button 
                    class="btn btn-sm toggle-status <?php echo ($row['emp_status'] == 1) ? 'btn-success' : 'btn-danger'; ?>" 
                    data-id="<?php echo $row['empid']; ?>" 
                    data-status="<?php echo $row['emp_status']; ?>">
                    <?php echo ($row['emp_status'] == 1) ? 'Active' : 'Inactive'; ?>
                </button>
            </td>
            <td><?php echo htmlspecialchars($row['emp_start']); ?></td>
            <td><?php echo htmlspecialchars($row['emp_endson']); ?></td>
            <td>
                <a class="btn" href="add.php?id=<?php echo $row['empid']; ?>">Edit</a> |
                <a class="btn" href="act_insert.php?id=<?php echo $row['empid']; ?>&action=3" onclick="return confirm('Are you sure you want to delete?');">Delete</a> | <a href="#" class="btn btn-info btn-view" data-id="<?php echo $row['empid']; ?>" data-bs-toggle="modal" data-bs-target="#empModal">View</a>

            </td>
        </tr>
    <?php endforeach; ?>
</table><br>

<!-- Pagination -->
<?php
$url = "<li class='page-item'><a class='page-link' href='list.php?page={pgNo}&search=" . urlencode($search) . "&dept=" . urlencode($selectedDept) . "&sortby=" . urlencode($sortBy) . "'>{pgTxt}</a></li>";
echo $obj_employee->getBootsPageLink($page, $totalPages, $url);
?>

<!-- Modal -->
<div class="modal fade" id="empModal" tabindex="-1" aria-labelledby="empModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="empModalLabel">Employee Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="empDetails"> 
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap and JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

//POPUP
$(document).ready(function(){
    $('.btn-view').click(function(){
        var empId = $(this).data('id');

        $.ajax({
            url: 'get_employee.php',
            type: 'GET',
            data: { id: empId },
            success: function(data){
                $('#empDetails').html(data);
                $('#empModal').modal('show');
            }
        });
    });
});
$('.toggle-status').click(function() {
    var button = $(this);
    var empId = button.data('id');
    var currentStatus = button.data('status');

    $.ajax({
        url: 'act_insert.php',
        type: 'POST',
        data: {
            action: 'toggle_status',
            empid: empId,
            current_status: currentStatus
        },
        success: function(response) {
            
            if (currentStatus == 1) {
                button.text('Inactive');
                button.removeClass('btn-success').addClass('btn-danger');
                button.data('status', 0);
            } else {
                button.text('Active');
                button.removeClass('btn-danger').addClass('btn-success');
                button.data('status', 1);
            }
        }
    });
});

</script>

<!-- PAGINATION -->
<?php
function getBootsPageLink($pgNo, $totPage, $url, $count = "2")
{
    $intPre = $pgNo - 1;
    $intNex = $pgNo + 1;
    $intFirst = $pgNo - 5;
    $intLast = $pgNo + 5;
    $strReturn = "";

    if ($intFirst <= 0) $intFirst = 1;
    if ($intLast >= $totPage) $intLast = $totPage;

    if ($intPre > 0) {
        $strTemp = str_replace("{pgNo}", "$intPre", $url);
        $strReturn .= str_replace("{pgTxt}", '&laquo;', $strTemp);
    }

    for ($i = $intFirst; $i <= $intLast; $i++) {
        if ($i != $pgNo) {
            $strTemp = str_replace("{pgNo}", "$i", $url);
            $strReturn .= str_replace("{pgTxt}", "$i", $strTemp);
        } else {
            $strReturn .= "<li class='page-item active'><a class='page-link' href='#'>$i</a></li>";
        }
    }

    if ($intNex <= $totPage) {
        $strTemp = str_replace("{pgNo}", "$intNex", $url);
        $strReturn .= str_replace("{pgTxt}", '&raquo;', $strTemp);
    }

    return '<nav><ul class="pagination justify-content-center mt-4">' . $strReturn . '</ul></nav>';
}
?>
</body>
</html>