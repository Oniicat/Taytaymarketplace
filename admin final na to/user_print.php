<?php
require('fpdf/fpdf.php'); // [IF INSIDE THE FOLDER]

// Create a custom PDF class
class PDF extends FPDF {
    // Add a header
    function Header() {
        // Logo Section
        $this->Image('logo.png', 90, 10, 30);
        $this->Ln(20);

        // Add title
        $this->SetFont('Helvetica', 'B', 16); // Use Helvetica font
        $this->Cell(0, 5, 'TAYTAY MARKETPLACE', 0, 1, 'C');
        $this->SetFont('Helvetica', '', 12); // Use Helvetica font
        $this->Cell(0, 6, 'Taytay, Rizal', 0, 1, 'C');
        $this->Cell(0, 5, date('F Y'), 0, 1, 'C'); // Display the current month and year
        $this->Ln(10);
    }
    
    // Add a footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8); // Use Helvetica font for footer
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Create a table
    function ProductTable($header, $data) {
        // Add the table title
        $this->SetFont('Helvetica', 'B', 14); // Use Helvetica font
        $this->SetTextColor(101, 45, 144); // Purple color
        $this->Cell(0, 10, 'TAYTAY MARKETPLACE SELLERS DATA', 0, 1, 'C');
        $this->Ln(5);

        // Add the table header
        $this->SetFont('Helvetica', 'B', 9); // Slightly smaller font for the header
        $this->SetFillColor(101, 45, 144); // Purple background
        $this->SetTextColor(255, 255, 255); // White text
        $widths = [10, 40, 40, 20, 30, 30, 25]; // Adjusted column widths
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($widths[$i], 8, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Add the table rows
        $this->SetFont('Helvetica', '', 8); // Slightly smaller font for rows
        $this->SetTextColor(1); // Black text
        foreach ($data as $row) {
            $this->Cell($widths[0], 8, $row['#'], 1, 0, 'C');
            $this->Cell($widths[1], 8, $row['seller_name'], 1, 0, 'L');
            $this->Cell($widths[2], 8, $row['shop_name'], 1, 0, 'L');
            $this->Cell($widths[3], 8, $row['stall_number'], 1, 0, 'L');
            $this->Cell($widths[4], 8, $row['business_permit_number'], 1, 0, 'L');
            $this->Cell($widths[5], 8, $row['municipality'], 1, 0, 'L');
            $this->Cell($widths[6], 8, number_format($row['products']), 1, 0, 'C');
            $this->Ln();
        }
    }
}

// Database connection
$host = "localhost";
$username = "root"; 
$password = "";     
$dbname = "webdev2";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$query = "SELECT su.seller_id as '#', CONCAT(si.first_name, ' ', si.last_name) AS seller_name, si.shop_name, si.stall_number, si.business_permit_number,
    si.municipality,  si.contact_number,
    COUNT(p.product_id) AS products
    FROM users su
    inner join shops si on su.seller_id = si.seller_id
    LEFT JOIN tb_products p ON si.seller_id = p.seller_id
    GROUP BY si.seller_id";
$result = $conn->query($query);

// Check if query is successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Column headers
$header = ['#', 'SELLER NAME', 'SHOP', 'STALL NO.', 'PERMIT', 'MUNICIPALITY', 'PRODUCTS'];

// Create PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->ProductTable($header, $data);
$pdf->Output();

// Close the database connection
$conn->close();
?>
