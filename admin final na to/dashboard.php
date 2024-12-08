<?php require 'function.php';?>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 50vh;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      width: 80%;
    }
    .card {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
    }
    .card h3 {
        align-items: start;
      margin: 0;
      font-size: 24px;
      color: #712798;
    }
    .card p {
      font-size: 32px;
      margin-top: 10px;
      color: #712798;
    }

    h1 {
      margin-left: 5%;
      color:#712798;
    }
    h2 {
      margin-left: 5%;
      color:#712798;
    }

  /* graph layout */

 .chart{
  align-items: center;
  display: flex;
  justify-content: center; /* Horizontally center */
  margin: 0; /* Remove default margin */
 }

/* Style for the chart container */
.chart-container {
  width: 100%;  /* Adjust width to your preference */
  max-width: 900px;  /* Max width for large screens */
  padding: 20px;
  border: 2px solid #ddd;  /* Light border around the chart */
  border-radius: 8px;  /* Rounded corners */
  background-color: #f9f9f9;  /* Light background color */

 
}

/* Style for the chart itself */
#myChart {
  width: 100% !important;  /* Ensure it fills the container */
  height: 400px;  /* Set a fixed height for the graph */
  background-color: #fff;  /* Set the chart's background to white */
  border-radius: 5px;  /* Rounded corners for the chart */
}

#UsageChart {
  width: 100% !important;  /* Ensure it fills the container */
  height: 400px;  /* Set a fixed height for the graph */
  background-color: #fff;  /* Set the chart's background to white */
  border-radius: 5px;  /* Rounded corners for the chart */
}


/*product table*/
/* Table Styles */
.container_table {
  max-width: 1200px;
  margin: 50px auto;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.product-table {
  width: 100%; /* Ensures the table takes up the full width of the container */
  border-collapse: collapse;
  margin-top: 20px;
}

.product-table th, .product-table td {
  text-align: center;
  padding: 12px;
  font-size: 1rem;
}

.product-table th {
  background-color: #6f42c1;
  color: #ccc;
  font-weight: bold;
}

.product-table tbody tr:nth-child(even) {
  background-color: #f2f2f2;
}

.product-table tbody tr:hover {
  background-color: #e9ecef;
}

/* Responsive Table */
@media (max-width: 900px) {
  .product-table th, .product-table td {
    font-size: 0.9rem;
    padding: 8px;
  }
}

.container_table table {
  width: 100%; /* Ensure table fits the container */
  overflow-x: auto; /* Adds horizontal scrolling for small screens if needed */
}
  </style>

<div class="container">
  <div class="dashboard">
    <!-- Sellers Card -->
    <div class="card">
      <h3>Total Users</h3>
      <p id="sellers-count">
      <?= getCount('users')?>
      </p>
    </div>

    <!-- Shops Card -->
    <div class="card">
      <h3>Active Users</h3>
      <p id="shops-count">
      <?= getCountactiveThisMonth('users')?>
      </p>
    </div>

     
    <!-- Products Card -->
    <div class="card">
      <h3>Registration</h3>
      <p id="products-count">
      <?= getCountThisMonth('registration');?>
      </p>
    </div>
  </div>
</div>





<h1>User Growth</h1>
<div class="chart">
  
<div class="chart-container">
  <canvas id="myChart"></canvas>
</div>

</div>


<h1>System Usage</h1>
<div class="chart">
  
<div class="chart-container">
  <canvas id="UsageChart"></canvas>
</div>

</div>




  
<?php 
// Fetch user growth data
$con = new mysqli('localhost', 'root', '', 'webdev2');
$query = $con->query("
  SELECT MONTHNAME(created_at) AS monthname, COUNT(*) AS user_count
  FROM users
  GROUP BY MONTH(created_at)
  ORDER BY MONTH(created_at)
");
$months = [
  'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
];
$userCounts = array_fill(0, 12, 0);
foreach ($query as $data) {
  $monthIndex = array_search($data['monthname'], $months);
  if ($monthIndex !== false) {
    $userCounts[$monthIndex] = $data['user_count'];
  }
}
$con->close();

// Fetch system usage data
$con = new mysqli('localhost', 'root', '', 'webdev2');
$query = $con->query("
  SELECT MONTHNAME(date_time) AS monthname, COUNT(*) AS user_count
  FROM activity_log
  GROUP BY MONTH(date_time)
  ORDER BY MONTH(date_time)
");
$userCountsUsage = array_fill(0, 12, 0);
foreach ($query as $data) {
  $monthIndex = array_search($data['monthname'], $months);
  if ($monthIndex !== false) {
    $userCountsUsage[$monthIndex] = $data['user_count'];
  }
}
$con->close();
?>





<?php
$sql = "
   SELECT 
    p.product_id, 
    p.shop_id, 
    p.product_name, 
    p.category, 
    IFNULL(pc.click_count, 0) AS click_count
FROM tb_products p
LEFT JOIN tb_product_clicks pc 
    ON p.product_id = pc.product_id
WHERE (p.category, IFNULL(pc.click_count, 0)) IN (
    SELECT 
        p2.category, 
        MAX(IFNULL(pc2.click_count, 0))
    FROM tb_products p2
    LEFT JOIN tb_product_clicks pc2 
        ON p2.product_id = pc2.product_id
    GROUP BY p2.category
)
ORDER BY click_count DESC";
//     SELECT 
//         p.product_id, 
//         p.seller_id, 
//         p.product_name, 
//         p.category, 
//         IFNULL(pc.click_count, 0) AS click_count
//     FROM tb_products p
//     LEFT JOIN tb_product_clicks pc 
//         ON p.product_id = pc.product_id
// ";

$result = $conn->query($sql);
?>
<h1>Popular Products By Category</h1>
<div class="container_table">
    <div class="product-table">
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Shop ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Views</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['product_id'] . "</td>";
                        echo "<td>" . $row['shop_id'] . "</td>";
                        echo "<td>" . $row['product_name'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "<td>" . $row['click_count'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Data for User Growth
  const userGrowthLabels = <?php echo json_encode($months); ?>;
  const userGrowthData = <?php echo json_encode($userCounts); ?>;
  
  const userGrowthConfig = {
    type: 'bar',
    data: {
      labels: userGrowthLabels,
      datasets: [{
        label: 'Users Registered Each Month',
        data: userGrowthData,
        backgroundColor: '#712798', 
        borderColor: '#712798',   
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  };
  new Chart(document.getElementById('myChart'), userGrowthConfig);

  // Data for System Usage
  const systemUsageLabels = <?php echo json_encode($months); ?>;
  const systemUsageData = <?php echo json_encode($userCountsUsage); ?>;
  
  const systemUsageConfig = {
    type: 'bar',
    data: {
      labels: systemUsageLabels,
      datasets: [{
        label: 'Number of Login Each Month',
        data: systemUsageData,
        backgroundColor: '#712798', 
        borderColor: '#712798',   
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  };
  new Chart(document.getElementById('UsageChart'), systemUsageConfig);
</script>
