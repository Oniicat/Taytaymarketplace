<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "webdev2";

$con = new mysqli($servername, $username, $password, $db_name);

if($con->connect_error){
    die("Connection failed".$con->connect_error);
}

$category_filter = isset($_POST['category']) ? $_POST['category'] : '';

$data_fetch = "SELECT p.product_id as '#' , p.product_name as product, p.category, si.shop_name as shop, concat(si.first_name, ' ', si.last_name) as seller, pc.click_count, p.date_created as date_posted
                from tb_products p
                left join shops si ON p.seller_id = si.seller_id
                left join tb_product_clicks pc on pc.product_id = p.product_id";

if ($category_filter !== "") {
    $data_fetch .= " WHERE p.category = ?";
} else {
    $data_fetch .= " WHERE 1";
}

if ($selection = $con->prepare($data_fetch)){
    $types = "";
    $params = [];

    if ($category_filter !== ""){
        $types .= "s";
        $params[] = $category_filter;
    }   

    if (!empty($types)) {
        $selection->bind_param($types, ...$params);
    }

    $selection->execute();
    $result = $selection->get_result();

    $reportData = [];
    while ($row = $result->FETCH_ASSOC()){
        $reportData[] = $row;
    }
    header ('Content-Type: application/json');
    echo json_encode($reportData);

    $selection->close();
} else {
    http_response_code(500);
    echo json_encode(['error'=> 'Failed to prepare the SQL query.']);
}

$con->close();
?>
