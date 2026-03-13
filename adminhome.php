<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') {
    header("location:admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "profiling_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, full_name, year_section, gender, address, email FROM register";
$result = $conn->query($sql);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

if (isset($_POST['ajaxSearch'])) {
    $searchQuery = $conn->real_escape_string($_POST['ajaxSearch']);
    $filterOptions = isset($_POST['filterOptions']) ? $_POST['filterOptions'] : [];

    $filterQueryParts = [];
    if (in_array('id', $filterOptions)) {
        $filterQueryParts[] = "id LIKE '%$searchQuery%'";
    }
    if (in_array('full_name', $filterOptions)) {
        $filterQueryParts[] = "full_name LIKE '%$searchQuery%'";
    }
    if (in_array('year_section', $filterOptions)) {
        $filterQueryParts[] = "year_section LIKE '%$searchQuery%'";
    }
    if (in_array('gender', $filterOptions)) {
        $filterQueryParts[] = "gender LIKE '%$searchQuery%'";
    }
    if (in_array('address', $filterOptions)) {
        $filterQueryParts[] = "address LIKE '%$searchQuery%'";
    }
    if (in_array('email', $filterOptions)) {
        $filterQueryParts[] = "email LIKE '%$searchQuery%'";
    }

    // If no filters are selected, return all results
    $sql = "SELECT id, full_name, year_section, gender, address, email FROM register";
    if (count($filterQueryParts) > 0) {
        $sql .= " WHERE " . implode(" OR ", $filterQueryParts);
    }

    $result = $conn->query($sql);
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode($students);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #add8e6, #87ceeb, #4682b4, #1e90ff, #00008b);
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: background 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #1abc9c;
            padding-left: 30px;
        }

        .main-content {
            margin-left: 270px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            margin-top: 30px;
            margin-right: 30px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar select, .search-bar input {
            padding: 12px;
            border-radius: 25px;
            border: 1px solid #ccc;
            outline: none;
            margin-right: 10px;
        }

        .search-bar button {
            padding: 12px 25px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }

        .dropdown-toggle {
            background-color: #1abc9c;
            border: none;
            padding: 12px 15px;
            border-radius: 25px;
            color: white;
            cursor: pointer;
        }

        .dropdown-menu {
            min-width: 150px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 8px;
            padding: 10px 0;
        }

        .dropdown-menu li a {
            padding: 10px 20px;
            display: block;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu li a:hover {
            background-color: #1abc9c;
            color: white;
        }

        @media print {
            td:last-child, th:last-child {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <a href="adminhome.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="admin.php"><i class="fas fa-cogs"></i> Manage Form</a>
        <a href="view_student.php"><i class="fas fa-users"></i> Manage Students</a>
        <a href="post_management.php"><i class="fas fa-bullhorn"></i> Post</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </ul>
</div>

<div class="main-content">
    <h2>Welcome, Admin!</h2>
    <div class="search-bar">
        <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="filterMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fas fa-bars"></i> Filter
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterMenu">
                <li><input type="checkbox" class="filter-option" value="id"> ID</li>
                <li><input type="checkbox" class="filter-option" value="full_name"> Full Name</li>
                <li><input type="checkbox" class="filter-option" value="year_section"> Year & Section</li>
                <li><input type="checkbox" class="filter-option" value="gender"> Gender</li>
                <li><input type="checkbox" class="filter-option" value="address"> Address</li>
                <li><input type="checkbox" class="filter-option" value="email"> Email</li>
            </ul>
        </div>
        <input type="text" id="searchInput" placeholder="Search students...">
        <button type="button" id="searchButton"><i class="fas fa-search"></i> Search</button>
    </div>

    <h3 style="display: inline-block;">Student List</h3>
    <div style="display: inline-block; margin-left: 10px;">
        <button onclick="printTable()" class="btn btn-success">
            <i class="fas fa-print"></i> Print
        </button>
        <div class="dropdown" style="display: inline-block; margin-left: 10px;">
            <button class="btn btn-primary dropdown-toggle" type="button" id="downloadMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fas fa-download"></i> Download
            </button>
            <ul class="dropdown-menu" aria-labelledby="downloadMenu">
                <li><a href="#" id="downloadWord">Download Word</a></li>
                <li><a href="#" id="downloadPDF">Download PDF</a></li>
                <li><a href="#" id="downloadExcel">Download Excel</a></li>
            </ul>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Year & Section</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Email</th>
            </tr>
        </thead>

        <tbody id="studentTableBody"></tbody>
    </table>
</div>
<!-- Modal for Student Details -->
<div id="studentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Student Details</h4>
            </div>
            <div class="modal-body">
            <p><strong>First Name:</strong> <span id="modalFirstName"></span></p>
<p><strong>Middle Name:</strong> <span id="modalMiddleName"></span></p>
<p><strong>Last Name:</strong> <span id="modalLastName"></span></p>
<p><strong>Gender:</strong> <span id="modalGender"></span></p>
<p><strong>Address:</strong> <span id="modalAddress"></span></p>
<p><strong>Email:</strong> <span id="modalEmail"></span></p>
<p><strong>Phone:</strong> <span id="modalPhone"></span></p>
<p><strong>Year & Section:</strong> <span id="modalYearSection"></span></p>
<p><strong>Status:</strong> <span id="modalStatus"></span></p>
<p><strong>Program:</strong> <span id="modalProgram"></span></p>

            </div>
             <!-- Alert message for form submission status -->
    <div id="modalFormAlert"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Load students and attach row click event
    function loadStudents(query = '', filters = []) {
        $.post('adminhome.php', { ajaxSearch: query, filterOptions: filters }, function (data) {
            const students = JSON.parse(data);
            let rows = '';
            if (students.length === 0) {
                rows = `<tr><td colspan="6" style="text-align:center;">No student record found</td></tr>`;
            } else {
                students.forEach(student => {
                    rows += `<tr class="student-row" data-id="${student.id}">
                        <td>${student.id}</td>
                        <td>${student.full_name}</td>
                        <td>${student.year_section}</td>
                        <td>${student.gender}</td>
                        <td>${student.address}</td>
                        <td>${student.email}</td>
                    </tr>`;
                });
            }
            $('#studentTableBody').html(rows);

            // Attach click event to student rows
            $('.student-row').click(function () {
                const studentId = $(this).data('id');
                showStudentModal(studentId);
            });
        });
    }

    // Show student modal with details
 // Function to show the modal with student details
function showStudentModal(studentId) {
    $.post('fetch_student_details.php', { studentId }, function (response) {
        const data = JSON.parse(response);
        
        // Extract data from the response with correct field names
        const { first_name, middle_name, last_name, gender, address, email, phone, year_section, status, program, forms, profile_picture } = data;

        // Populate modal fields with student details
        $('#modalFirstName').text(first_name); // First Name
        $('#modalMiddleName').text(middle_name); // Middle Name
        $('#modalLastName').text(last_name); // Last Name
        $('#modalGender').text(gender);
        $('#modalAddress').text(address);
        $('#modalEmail').text(email);
        $('#modalPhone').text(phone);
        $('#modalYearSection').text(year_section); // Display year_section as it is
        $('#modalStatus').text(status);
        $('#modalProgram').text(program);

        // Add profile picture to the modal
        if (profile_picture) {
            $('#modalProfilePicture').attr('src', 'uploads/' + profile_picture);  // Assuming profile_picture contains the filename
        } else {
            $('#modalProfilePicture').attr('src', 'uploads/default.jpg');  // Default image if profile picture doesn't exist
        }

        // Check if the student has submitted any forms
        let formHtml = '';
        let alertMessage = '';
        if (forms.length > 0) {
            forms.forEach(form => {
                formHtml += `<p>${form}</p>`;
            });
            alertMessage = '<div class="alert alert-success">This student has already submitted the form(s).</div>';
        } else {
            formHtml = '<p>No forms submitted.</p>';
            alertMessage = '<div class="alert alert-warning">This student has not submitted any forms yet.</div>';
        }
        
        // Populate form submissions (if any)
        $('#modalForms').html(formHtml);
        
        // Display alert message about form submission status
        $('#modalFormAlert').html(alertMessage);

        // Show the modal
        $('#studentModal').modal('show');
    });
}


// Initial load (for example)
loadStudents();

});
    function printTable() {
        const printTable = document.querySelector('.table').cloneNode(true);
        const rows = printTable.querySelectorAll('tr');
        
        rows.forEach(row => {
            const actionColumn = row.querySelector('td:last-child');
            if (actionColumn) {
                actionColumn.style.display = 'none';  // Hide the action column in the print version
            }
        });

        const printContents = printTable.outerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <html>
                <head>
                    <title>Print Student List</title>
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>
                    <h2 style="text-align:center;">Student List</h2>
                    ${printContents}
                </body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        location.reload();  // Reload to restore the page after printing
    }

    // Handle download options based on selected format
$(document).ready(function () {
    $('#downloadWord').click(function () {
        downloadWord();
    });

    $('#downloadPDF').click(function () {
        downloadPDF();
    });

    $('#downloadExcel').click(function () {
        downloadExcel();
    });
});

function downloadWord() {
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tr');
    let wordData = '';
    
    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td, th');
        cells.forEach(cell => {
            wordData += cell.innerText + '\t';
        });
        wordData += '\n';
    });

    // Create a Blob for Word file (or text file as .docx extension)
    const blob = new Blob([wordData], { type: 'application/msword' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'student_list.docx';
    link.click();
}


// Function to download as PDF
function downloadPDF() {
    const doc = new jsPDF();
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tr');
    const content = [];
    
    // Iterate over rows, extract cells' text, and store in content
    rows.forEach((row, index) => {
        const rowData = [];
        const cells = row.querySelectorAll('td, th');
        cells.forEach(cell => {
            rowData.push(cell.innerText.trim());
        });
        content.push(rowData);
    });

    // Generate the PDF table
    doc.autoTable({
        head: [content[0]],   // Header row (first row of table)
        body: content.slice(1)  // Data rows (rest of the table)
    });

    // Save the PDF
    doc.save('student_list.pdf');
}

// Function to download as Excel
function downloadExcel() {
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tr');
    const data = [];

    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td, th');
        const rowData = [];
        cells.forEach(cell => {
            rowData.push(cell.innerText);
        });
        data.push(rowData);
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, "Student List");

    // Create Excel file and trigger download
    XLSX.writeFile(wb, 'student_list.xlsx');
}

</script>

<!-- jQuery (already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (for dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS (for dropdown functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script>$(document).ready(function () {
    // Function to load students
    function loadStudents(query = '', filters = []) {
        $.post('adminhome.php', { ajaxSearch: query, filterOptions: filters }, function (data) {
            const students = JSON.parse(data);
            let rows = '';
            if (students.length === 0) {
                rows = `<tr><td colspan="6" style="text-align:center;">No student record found</td></tr>`;
            } else {
                students.forEach(student => {
                    rows += `<tr class="student-row" data-id="${student.id}">
                        <td>${student.id}</td>
                        <td>${student.full_name}</td>
                        <td>${student.year_section}</td>
                        <td>${student.gender}</td>
                        <td>${student.address}</td>
                        <td>${student.email}</td>
                    </tr>`;
                });
            }
            $('#studentTableBody').html(rows);

            $(document).on('click', '.student-row', function () {
    const studentId = $(this).data('id');
    showStudentModal(studentId);
});

        });
    }

  // Function to show the modal with student details
// Function to show the modal with student details
// Function to show the modal with student details
function showStudentModal(studentId) {
    $.post('fetch_student_details.php', { studentId }, function (response) {
        const data = JSON.parse(response);
        
        // Extract data from the response with correct field names
        const { first_name, middle_name, last_name, gender, address, email, phone, year_section, status, program, forms, profile_picture } = data;

        // Populate modal fields with student details
        $('#modalFirstName').text(first_name); // First Name
        $('#modalMiddleName').text(middle_name); // Middle Name
        $('#modalLastName').text(last_name); // Last Name
        $('#modalGender').text(gender);
        $('#modalAddress').text(address);
        $('#modalEmail').text(email);
        $('#modalPhone').text(phone);
        $('#modalYearSection').text(year_section); // Display year_section as it is
        $('#modalStatus').text(status);
        $('#modalProgram').text(program);

        // Add profile picture to the modal
        if (profile_picture && profile_picture !== 'default.jpg') {
            $('#modalProfilePicture').attr('src', 'uploads/' + profile_picture);  // Assuming profile_picture contains the filename
        } else {
            $('#modalProfilePicture').attr('src', 'uploads/default.jpg');  // Default image if profile picture doesn't exist or is set to 'default.jpg'
        }

        // Check if the student has submitted any forms
        let formHtml = '';
        let alertMessage = '';
        if (forms && forms.length > 0) {
            forms.forEach(form => {
                formHtml += `<p>${form}</p>`;
            });
            alertMessage = '<div class="alert alert-success">This student has already submitted the form(s).</div>';
        } else {
            formHtml = '<p>No forms submitted.</p>';
            alertMessage = '<div class="alert alert-warning">This student has not submitted any forms yet.</div>';
        }
        
        // Populate form submissions (if any)
        $('#modalForms').html(formHtml);
        
        // Display alert message about form submission status
        $('#modalFormAlert').html(alertMessage);

        // Show the modal
        $('#studentModal').modal('show');
    });
}


// Initial load (for example)
loadStudents();


});
</script>

</body>
</html>
