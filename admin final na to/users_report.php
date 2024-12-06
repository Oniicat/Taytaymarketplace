<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USERS</title>
    <link rel="stylesheet" href="user_report_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>

<body>
    <div class="container">
        <h1>Seller</h1>
        <div class="filter-bar">
            <input type="text" name="search_field" id="search_field" placeholder="Search" class="search-box">
        </div>

        <div class="table-container">
            <table id="users_table" class="min-w-full bg-white border border-gray-300">
                <tbody>
                    <!-- Report rows will be inserted here -->
                </tbody>
            </table>
        </div>

        <!-- DOWNLOAD BUTTON -->
        <div class="download">
            <a id="download_pdf" href="user_print.php" target="_blank">
                <button>Download PDF</button>
            </a>
        </div>

        </div>

</body>


<script>
    // Fetch all product data when the page loads and whenever the category filter is changed
    document.addEventListener('DOMContentLoaded', function() {
    fetch_report_data();  // Load all products when the page is loaded
    });
    document.getElementById('municipality_filter').addEventListener('change', update_report);
    
    function update_report() {
        let selected_municipality = document.getElementById('municipality_filter').value;
        fetch_report_data(selected_municipality)
    }
    
    // Function to fetch and display data based on the selected filters
    function fetch_report_data(municipality_filter){
        $.ajax({
            url: 'users_report_connection.php',
            method: 'POST',
            data: {
                municipality: municipality_filter,
            },
            dataType: 'json',
            success: function(data) {
            let reportData = data;
            let tableBody = document.querySelector('#users_table tbody');
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
                        noDataCell.setAttribute('colspan', 6);  // Adjust column span based on your table structure
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
    let rows = document.querySelectorAll('#users_table tbody tr');
    rows.forEach((row, index) => {
    // Skip header row
    if (index === 0) return;
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
    });
});
</script>



</html>
