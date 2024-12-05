<?php
include 'conn.php';

try {
    $query = "SELECT seller_id, email, password, created_at FROM accounts";
    $result = $conn->query($query);

    // Fetch all results
    $accounts = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellers</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Seller ID</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $index => $account): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($account['seller_id']) ?></td>
                    <td><?= htmlspecialchars($account['email']) ?></td>
                    <td><?= htmlspecialchars($account['password']) ?></td>
                    <td><?= htmlspecialchars($account['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
