<?php
// Start the session
session_start();

// Check if the user session variable is set
if (!isset($_SESSION["user"]["id"])) {
    die("User session not found. Please log in."); // Handle the case where user is not logged in
}

// Retrieve user ID from the session
$user_id = $_SESSION["user"]["id"]; // Use $_SESSION["user"]["id"] to retrieve user ID

// Include the database connection file
include_once "database.php";

// Check if the database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission to update status
// Handle form submission to update status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST); // Debugging: Check form data
    // Retrieve data from $_POST array
    $risk_id = $_POST['risk_id'];
    $status = $_POST['status'];

    // Escape special characters and prevent SQL injection
    $status = mysqli_real_escape_string($conn, $status);

    // Prepare and execute SQL statement to insert data into the review table
    $insert_query = "INSERT INTO review (id, risk_id, status) VALUES ('$user_id', '$risk_id', '$status')";
    if (mysqli_query($conn, $insert_query)) {
        // Data inserted successfully
        echo "Review data saved successfully. Redirecting...";
        header("Refresh:2; url=dashboard.php"); // Redirect to dashboard after 2 seconds
        exit();
    } else {
        // Error occurred while inserting data
        echo "Error: " . $insert_query . "<br>" . mysqli_error($conn);
    }
}


// Prepare the SQL query to fetch data only for the specific user's submitted risks
$query = "SELECT m.id AS risk_id, m.subject, m.riskMapping, mi.mitigation, c.Controls
          FROM message m
          LEFT JOIN mitigation mi ON m.id = mi.risk_id
          LEFT JOIN controls c ON m.riskMapping = c.riskMapping
          WHERE m.user_id = '$user_id'";

$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Check if there are any results
if (mysqli_num_rows($result) > 0) {
    // Output table header
    echo "<table>
            <tr>
                <th>User Name</th>
                <th>Subject</th>
                <th>Risk Mapping</th>
                <th>Provided Mitigation</th>
                <th>Status</th>
                <th>Controls</th>
            </tr>";

    // Output data rows
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $_SESSION["user"]["full_name"] . "</td>"; // Output user name
        echo "<td>" . $row['subject'] . "</td>";
        echo "<td>" . $row['riskMapping'] . "</td>";
        echo "<td>" . $row['mitigation'] . "</td>";
        
        // Output dropdown for status
        echo "<td>";
        echo "<form method='post'>"; // Form for status update
        echo "<select name='status'>";
        echo "<option value='Active'>Active</option>";
        echo "<option value='In Progress'>In Progress</option>";
        echo "<option value='Resolved'>Resolved</option>";
        echo "</select>";
        echo "<input type='hidden' name='risk_id' value='" . $row['risk_id'] . "'>"; // Hidden input for risk ID
        echo "<input type='submit' value='Update'>"; // Submit button
        echo "</form>";
        echo "</td>";

        // Display controls fetched from the controls table
        echo "<td>" . $row['Controls'] . "</td>";
        echo "</tr>";
    }

    // Close table
    echo "</table>";
} else {
    // If no results found
    echo "No risks to display.";
}

// Close the database connection
mysqli_close($conn);
?>



<html>
    <head>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    select {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    input[type="submit"] {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }
</style>
<style>
    /* Existing CSS styles */
    
    input[type="submit"] {
        padding: 5px 10px;
        background-color: #4CAF50; /* Green color */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #45a049; /* Darker green color on hover */
    }
    input[type="submit"] {
    padding: 5px 10px;
    background-color: #007bff; /* Blue color */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

</style>

    </head>
</html>