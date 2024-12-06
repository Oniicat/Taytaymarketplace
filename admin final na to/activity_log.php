<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity LOGG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #8e44ad;
        }

        .panel {
            max-width: 1500px;
            margin: 50px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #8e44ad;
            color: white;
            font-weight: bold;
        }

        td:first-child {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9e9e9;
        }
    </style>
</head>

<body>
    <h1>Activity Log</h1>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Activity Type</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
        <tbody>
            <?php
                session_start();

                function getDatabaseConnection() {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "webdev2";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    return $conn;
                }

                $conn = getDatabaseConnection();

                // SQL query
                $sql = "SELECT user_name, activity_type, date_time FROM activity_log";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["user_name"] . "</td>";
                        echo "<td>" . $row["activity_type"] . "</td>";
                        echo "<td>" . $row["date_time"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No activity logs found.</td></tr>";
                }

                $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>