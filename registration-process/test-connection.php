<?php
include 'conn.php'; // Include the database connection file

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Query to fetch the current database name
    $dbResult = $conn->query("SELECT DATABASE() AS dbname");
    $dbName = $dbResult->fetch_assoc()['dbname'];
    
    // Query to fetch the tables from the database
    $result = $conn->query("SHOW TABLES");
    
    if ($result && $result->num_rows > 0) {
        echo "Database connected successfully. Tables in the database:<br>";
        
        // Fetch and display each table name
        while ($row = $result->fetch_array()) {
            // Dynamically fetch the table name based on the database
            echo $row['Tables_in_' . $dbName] . "<br>";
        }
    } else {
        echo "No tables found or there was an error with the query.";
    }
} catch (Exception $e) {
    // Handle any query or connection errors
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
