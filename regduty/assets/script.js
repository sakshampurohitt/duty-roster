document.getElementById('toggleButton').addEventListener('click', function() {
    const loginTitle = document.getElementById('login-title');
    const userType = document.getElementById('userType');
    const toggleButton = document.getElementById('toggleButton');

    if (userType.value === 'employee') {
        userType.value = 'admin';
        loginTitle.textContent = 'Admin Login';
        toggleButton.textContent = 'Login as Employee';
    } else {
        userType.value = 'employee';
        loginTitle.textContent = 'Employee Login';
        toggleButton.textContent = 'Login as Admin';
    }
});

function confirmEmployeeDeletion() {
    const empid = document.getElementById('empid').value;
    if (confirm(`Are you sure you want to delete employee with ID ${empid}?`)) {
        document.getElementById('delete_employee_form').submit();
    }
}

function confirmScheduleDeletion(scheduleid) {
    if (confirm(`Are you sure you want to delete schedule with ID ${scheduleid}?`)) {
        document.getElementById('delete_schedule_form').submit();
    }
}

document.getElementById('insert_employee_form').addEventListener('submit', function(event) {
    event.preventDefault();
    this.submit();
});

document.getElementById('add_schedule_form').addEventListener('submit', function(event) {
    event.preventDefault();
    this.submit();
});

document.getElementById('logout_link').addEventListener('click', function(event) {
    event.preventDefault();
    window.location.href = 'admindash.php?logout';
});

function confirmDepartmentDeletion() {
    const departmentid = document.getElementById('departmentid').value;
    if (confirm(`Are you sure you want to delete department with ID ${departmentid} and all associated employees?`)) {
        document.getElementById('delete_department_form').submit();
    }
}


document.getElementById('insert_department_form').addEventListener('submit', function(event) {
    event.preventDefault();
    this.submit();
});

// Function to handle confirmation for employee deletion
function confirmEmployeeDeletion() {
    const empid = document.getElementById('empid').value;
    if (confirm(`Are you sure you want to delete employee with ID ${empid}?`)) {
        document.getElementById('delete_employee_form').submit();
    }
}

// Function to handle confirmation for schedule deletion
function confirmScheduleDeletion(scheduleid) {
    if (confirm(`Are you sure you want to delete schedule with ID ${scheduleid}?`)) {
        document.getElementById('delete_schedule_form').submit();
    }
}

// Event listener for employee deletion form submission prevention
document.getElementById('delete_employee_form').addEventListener('submit', function(event) {
    event.preventDefault();
});

// Event listener for schedule deletion form submission prevention
document.getElementById('delete_schedule_form').addEventListener('submit', function(event) {
    event.preventDefault();
});
function confirmScheduleDeletion() {
    const scheduleid = document.getElementById('scheduleid').value;
    if (confirm(`Are you sure you want to delete schedule with ID ${scheduleid}?`)) {
        document.getElementById('delete_schedule_form').submit();
    }
}

// Event listener for department deletion form submission prevention
document.getElementById('delete_department_form').addEventListener('submit', function(event) {
    event.preventDefault();
});
