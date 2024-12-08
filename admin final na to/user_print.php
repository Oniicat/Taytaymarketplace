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
        $widths = [15, 30, 40, 25, 30, 30, 25]; // Adjusted column widths after removing shop description

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
        
            // Pre-calculate the maximum height for the row
            $maxHeight = 0;
            $cellHeights = [];
            foreach ($cellData as $i => $text) {
                // Create a temporary MultiCell to calculate required height
                $nbLines = $this->NbLines($widths[$i], $text); // Calculate number of lines needed
                $cellHeight = $nbLines * 6; // Each line has a height of 6
                $cellHeights[$i] = $cellHeight;
        
                if ($cellHeight > $maxHeight) {
                    $maxHeight = $cellHeight; // Update maximum height for the row
                }
            }
        
            // Render each cell with consistent row height
            foreach ($cellData as $i => $text) {
                $x = $this->GetX();
                $y = $this->GetY();
        
                // Draw a cell box with maximum height, even for MultiCell
                $this->Rect($x, $y, $widths[$i], $maxHeight);
        
                // Print content using MultiCell within the defined box
                $this->MultiCell($widths[$i], 6, $text, 0, 'C');
        
                // Set the X position for the next cell
                $this->SetXY($x + $widths[$i], $y);
            }
        
            // Move to the next row
            $this->Ln($maxHeight);
        }
        
        
    }
    function NbLines($width, $text) {
        $cw = $this->CurrentFont['cw'];
        if ($width == 0) {
            $width = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($width - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $text);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
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
