<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "webdev2";

$con = new mysqli($servername, $username, $password, $db_name);

if($con->connect_error){
    die("Connection failed".$con->connect_error);
}

//su = data coming from tb_signup
//si = data coming form tb_shop_info
//p = data coming from tb_products
$data_fetch = "SELECT u.seller_id as '#', CONCAT(si.first_name, ' ', si.last_name) AS seller_name, si.shop_name, si.stall_number, si.business_permit_number,
    si.municipality,  si.contact_number,
    COUNT(p.product_id) AS product_count
    FROM users u
    inner join shops si on u.seller_id = si.seller_id
    LEFT JOIN tb_products p ON si.seller_id = p.seller_id GROUP BY si.seller_id";

if ($selection = $con->prepare($data_fetch)){
    $types = "";
    $params = [];

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
    exit;
}

$con->close();
?>
