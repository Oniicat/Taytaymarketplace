<?php
   session_start();
   require 'dbcon.php';
    

    function validate($inputData)
    {
        global $conn;
        return mysqli_real_escape_string($conn, $inputData);
    }

    function redirect($url, $status )
    {
        $_SESSION['status'] = $status;
        header('Location: ' . $url);
        exit(0);
    }

    function alertMessage() {
        if(isset($_SESSION['status']))
        {
                echo '<div class = "alert alert-success">
                <h4>'.$_SESSION['status'].'</h4>
                </div>';
                unset($_SESSION['status']);
        }
    }

//for counting rows in table
    function getcount($tablename)
    {
        global $conn; //sa loob nang variable na yan connection mo sa db
        $table = validate($tablename);
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
        $totalCount = mysqli_num_rows($result);
        return $totalCount;
    }
//ganto sample ng pag callout  <h1>"getCount('admin')"</h1> yung admin yung pangalan ng table

function getCountThisMonth($tableName)
{
    global $conn; // Use the database connection

    // Sanitize the table name to avoid SQL injection
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);

    // If the sanitized table name is empty, return an error
    if (empty($tableName)) {
        echo "Invalid table name provided.";
        return 0;
    }

    // Define the start of the current month (00:00:00) and the end of today (23:59:59)
    $startOfMonth = date('Y-m-01 00:00:00'); 
    $endOfMonth = date('Y-m-t 23:59:59');    

    // Build the SQL query using prepared statements
    $sql = "SELECT COUNT(*) AS totalCount FROM `$tableName` WHERE `created_at` BETWEEN ? AND ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Failed to prepare the query: " . $conn->error;
        return 0;
    }

    // Bind the parameters to the query
    $stmt->bind_param("ss", $startOfMonth, $endOfMonth);

    // Execute the query
    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->error;
        $stmt->close();
        return 0;
    }

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the count or 0 if no data
    return $row['totalCount'] ?? 0;
}

function getCountactiveThisMonth($tableName)
{
    global $conn; // Use the database connection

    // Sanitize the table name to avoid SQL injection
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);

    // If the sanitized table name is empty, return an error
    if (empty($tableName)) {
        echo "Invalid table name provided.";
        return 0;
    }

    // Define the start of the current month (00:00:00) and the end of today (23:59:59)
    $startOfMonth = date('Y-m-01 00:00:00'); 
    $endOfMonth = date('Y-m-07 23:59:59');    

    // Build the SQL query using prepared statements
    $sql = "SELECT COUNT(*) AS totalCount FROM `$tableName` WHERE `lastlogin_time` BETWEEN ? AND ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Failed to prepare the query: " . $conn->error;
        return 0;
    }

    // Bind the parameters to the query
    $stmt->bind_param("ss", $startOfMonth, $endOfMonth);

    // Execute the query
    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->error;
        $stmt->close();
        return 0;
    }

    // Fetch the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the count or 0 if no data
    return $row['totalCount'] ?? 0;
}
