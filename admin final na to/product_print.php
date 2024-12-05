<?php
require('fpdf/fpdf.php');

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
            $this->Cell($widths[0], 8, $row['#'], 1, 0, 'C');
            $this->Cell($widths[1], 8, $row['product'], 1, 0, 'L');
            $this->Cell($widths[2], 8, $row['category'], 1, 0, 'L');
            $this->Cell($widths[3], 8, $row['shop'], 1, 0, 'L');
            $this->Cell($widths[4], 8, $row['seller'], 1, 0, 'L');
            $this->Cell($widths[5], 8, number_format($row['click_count']), 1, 0, 'C');
            $this->Cell($widths[6], 8, $row['date_posted'], 1, 0, 'C');
            $this->Ln();
        }
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

$stmt->close();
$con->close();
?>
