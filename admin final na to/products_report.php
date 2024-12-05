<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="products_report_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <div class="container">
        <h1>Products</h1>
        <div class="filter-bar">
            <input type="text" name="search_field" id="search_field" placeholder="Search" class="search-box">
            <select name="category_filter" id="category_filter" class="category-filter">
                <option value="">Select Category</option>
                <?php
                
                    $host = 'localhost';
                    $db = 'webdev2';
                    $user = 'root';
                    $pass = '';
                
                    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    if($con->connect_error){
                        die("Connection failed".$con->connect_error);
                    }

                    try {
                            $fetch_category = $pdo;
                            $category_get = $fetch_category->prepare('SELECT DISTINCT category FROM tb_products');
                            $category_get->execute();
    
                            if ($category_get->rowCount() > 0) {
                                $category = $category_get->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($category as $category_set) {
                                    echo '<option value="' . htmlspecialchars($category_set['category']) . '">' . htmlspecialchars($category_set['category']) . '</option>';
                                }
                            }
                        } 
                catch (PDOException $error) {
                    echo "Error: " . htmlspecialchars($error->getMessage());
                }
            ?>
            </select>
        </div>

        <div class="table-container">
            <table id="product_table" class="min-w-full bg-white border border-gray-300">
                <tbody>
                    <!-- Report rows will be inserted here -->
                </tbody>
            </table>
        </div>

        <!-- DOWNLOAD BUTTON -->
        <div class="download">
            <a id="download_pdf" href="product_print.php" target="_blank">
                <button>Download PDF</button>
            </a>
        </div>
    </div>

</body>

<script>
    document.getElementById('category_filter').addEventListener('change', function () {
        let selected_category = this.value;
        let download_link = document.getElementById('download_pdf');
        let url = 'product_print.php';

        // Append category filter to the URL if selected
        download_link.href = selected_category ? `${url}?category=${encodeURIComponent(selected_category)}` : url;
    });
</script>


<script>
    // Fetch all product data when the page loads and whenever the category filter is changed
    document.addEventListener('DOMContentLoaded', function() {
    fetch_report_data();  // Load all products when the page is loaded
    });
    document.getElementById('category_filter').addEventListener('change', update_report);
    
    function update_report() {
        let selected_category = document.getElementById('category_filter').value;
        fetch_report_data(selected_category)
    }
    
    // Function to fetch and display data based on the selected filters
    function fetch_report_data(category_filter){
        $.ajax({
            url: 'products_report_connection.php',
            method: 'POST',
            data: {
                category: category_filter,
            },
            dataType: 'json',
            success: function(data) {
            let reportData = data;
            let tableBody = document.querySelector('#product_table tbody');
            tableBody.innerHTML = "";
            if (reportData.length > 0){
                let tableheaders = Object.keys(reportData[0]);
                let headerRow = document.createElement("tr");
                tableheaders.forEach(header => {
                    let th = document.createElement('th');
                    th.classList.add('px-4', 'py-2', 'border');
                    th.textContent = header.replace(/_/g, " ").toUpperCase();
                    headerRow.appendChild(th);
                });
                tableBody.appendChild(headerRow);
                reportData.forEach(row => {
                    let tableRow = document.createElement('tr');
                    tableheaders.forEach(header=>{
                        let td = document.createElement('td');
                        td.classList.add('px-4', 'py-2', 'border');
                        td.textContent = row[header];
                        tableRow.appendChild(td);
                    });
                    tableBody.appendChild(tableRow);
                }); 
            }  else {
                        let noDataRow = document.createElement('tr');
                        let noDataCell = document.createElement('td');
                        noDataCell.setAttribute('colspan', 6);
                        noDataCell.textContent = 'No products found with the selected category';
                        noDataRow.appendChild(noDataCell);
                        tableBody.appendChild(noDataRow);
                    }
        },
            error: function(error){
                console.error('Error Fetching Data:', error);
            }
        });
    }

    document.getElementById('search_field').addEventListener('keyup', function() {
    let query = this.value.toLowerCase();
    let rows = document.querySelectorAll('#product_table tbody tr');
    rows.forEach((row, index) => {
    if (index === 0) return;
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
    });
});
</script>



</html>
