<?php
$host = 'localhost';  // MySQL host
$username = 'root';  // MySQL username
$password = '';  // MySQL password
$database = 'students';  // Name of your database

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate the form data
function validateFormData($formData) {
    $errors = array();

    // Validate full name
    if (empty($formData['fullname'])) {
        $errors[] = 'Full name is required';
    }

    // Validate email
    if (empty($formData['email'])) {
        $errors[] = 'Email address is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    // Validate gender
    if (empty($formData['gender'])) {
        $errors[] = 'Gender is required';
    }

    return $errors; // Return an array of errors
}

// Validate form data
$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateFormData($_POST);

    if (empty($errors)) {
        // Sanitize the form data
        $fullname = $conn->real_escape_string($_POST['fullname']);
        $email = $conn->real_escape_string($_POST['email']);
        $gender = $conn->real_escape_string($_POST['gender']);

        // Insert data into the "students" table
        $sql = "INSERT INTO students (full_name, email, gender) VALUES ('$fullname', '$email', '$gender')";

        if ($conn->query($sql) === TRUE) {
            $successMessage = 'Data inserted successfully';
        } else {
            $errors[] = 'Error inserting data: ' . $conn->error;
        }
    }
}

// Retrieve student data from the "students" table
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
</head>
<body>
<h1>Registration Form</h1>
<form method="POST" action="insert_student.php">
    <label for="fullname">Full Name:</label>
    <input type="text" id="fullname" name="fullname" required><br><br>

    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label>Gender:</label><br>
    <input type="radio" id="male" name="gender" value="Male">
    <label for="male">Male</label><br>
    <input type="radio" id="female" name="gender" value="Female">
    <label for="female">Female</label><br><br>

    <input type="submit" value="Submit">
</form>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
        <?php endforeach; ?>
    </ul>
<?php elseif (isset($successMessage)): ?>
    <p><?php echo $successMessage; ?></p>
<?php endif; ?>

<h2>Registered Students</h2>
<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Gender</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['gender']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No students registered yet</p>
<?php endif; ?>
</body>
</html>
