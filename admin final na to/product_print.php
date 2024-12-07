<?php
require('fpdf/fpdf.php');
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "webdev2";

$con = new mysqli($servername, $username, $password, $db_name);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch the selected category
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// SQL Query
$data_fetch = "SELECT p.product_id as '#' , p.product_name as product, p.category, si.shop_name as shop, concat(si.first_name, ' ', si.last_name) as seller, pc.click_count, p.date_created as date_posted
                from tb_products p
                left join shops si ON p.seller_id = si.seller_id
                left join tb_product_clicks pc on pc.product_id = p.product_id";
if ($category_filter !== "") {
    $data_fetch .= " WHERE p.category = ?";
}

$stmt = $con->prepare($data_fetch);
if ($category_filter !== "") {
    $stmt->bind_param("s", $category_filter);
}

$stmt->execute();
$result = $stmt->get_result();

// Prepare data for PDF
$reportData = [];
while ($row = $result->fetch_assoc()) {
    $reportData[] = $row;
}

// Define custom PDF class
class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image('logo.png', 90, 10, 30);
        $this->Ln(20);

        // Title
        $this->SetFont('Helvetica', 'B', 16);
        $this->Cell(0, 5, 'TAYTAY MARKETPLACE', 0, 1, 'C');
        $this->SetFont('Helvetica', '', 12);
        $this->Cell(0, 6, 'Taytay, Rizal', 0, 1, 'C');
        $this->Cell(0, 5, date('F Y'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function ProductTable($header, $data)
    {
        // Table title
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetTextColor(101, 45, 144);
        $this->Cell(0, 10, 'TAYTAY MARKETPLACE PRODUCTS DATA', 0, 1, 'C');
        $this->Ln(5);

        // Table header
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetFillColor(101, 45, 144);
        $this->SetTextColor(255, 255, 255);
        $widths = [10, 40, 30, 30, 25, 20, 40];
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($widths[$i], 8, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Table rows
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(0);
        foreach ($data as $row) {
        $cellData =[
            $row['#'],
            $row['product'],
            $row['category'],
            $row['shop'],
            $row['seller'],
            number_format($row['click_count']),
            $row['date_posted']
        ];
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
// Generate the PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 12);

// Table headers
$header = ['#', 'Product', 'Category', 'Shop', 'Seller', 'Views', 'Date Posted'];

// Add table to the PDF
$pdf->ProductTable($header, $reportData);

// Output the PDF to browser
$pdf->Output('I', 'Product_Report.pdf'); // 'I' outputs to the browser instead of downloading



//activity log ni josh mojica(nakikita ka nya, dapat masipag ka)
$activityType = "Print Report";
$insert_sql = "INSERT INTO activity_log (user_name, activity_type, date_time) VALUES (?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ss", $userEmail, $activityType); //since naka session_start() yung $userEmail na variable ba ay universal
$insert_stmt->execute();
$insert_stmt->close();



$stmt->close();
$con->close();
?>
