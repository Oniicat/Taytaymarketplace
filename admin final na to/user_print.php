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

    function ProductTable($header, $data) {
        // Add the table title
        $this->SetFont('Helvetica', 'B', 14); // Use Helvetica font
        $this->SetTextColor(101, 45, 144); // Purple color
        $this->Cell(0, 10, 'TAYTAY MARKETPLACE SELLERS DATA', 0, 1, 'C');
        $this->Ln(5);

        // Define column widths
        $widths = [15, 30, 20, 25, 40, 40, 25]; // Adjusted column widths after removing shop description

        // Add the table header
        $this->SetFont('Helvetica', 'B', 9); // Slightly smaller font for the header
        $this->SetFillColor(101, 45, 144); // Purple background
        $this->SetTextColor(255, 255, 255); // White text
        foreach ($header as $i => $col) {
            $this->Cell($widths[$i], 8, $col, 1, 0, 'C', true);
        }
        $this->Ln();

        // Add the table rows
        $this->SetFont('Helvetica', '', 8); // Slightly smaller font for rows
        $this->SetTextColor(0); // Black text
        foreach ($data as $row) {
            $cellData = [
                $row['#'],
                $row['seller_name'],
                $row['shop_name'],
                $row['stall_number'],
                $row['business_permit_number'],
                $row['municipality'],
                number_format($row['products'])
            ];

            // Calculate the maximum height for the row
            $maxHeight = 0;
            $multiCellHeights = [];
            foreach ($cellData as $i => $text) {
                // Calculate the number of lines for each cell based on width
                $lineCount = ceil($this->GetStringWidth($text) / $widths[$i]);
                // Calculate the height for each cell
                $cellHeight = $lineCount * 6; // Default line height
                $multiCellHeights[$i] = $cellHeight;

                // Update the maximum row height
                if ($cellHeight > $maxHeight) {
                    $maxHeight = $cellHeight;
                }
            }

            // Draw each cell in the row
            foreach ($cellData as $i => $text) {
                $x = $this->GetX();
                $y = $this->GetY();
                $this->MultiCell($widths[$i], 6, $text, 1, 'L');
                $this->SetXY($x + $widths[$i], $y); // Move to the next cell
            }

            $this->Ln($maxHeight); // Move to the next row based on the tallest cell height
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
$pdf = new PDF('P', 'mm', 'A4'); // Portrait orientation
$pdf->AddPage();
$pdf->ProductTable($header, $data);
$pdf->Output();

// Close the database connection
$conn->close();
?>
