<?php
// Enable CORS for all origins
header("Access-Control-Allow-Origin: *");

// Allow the following methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Allow the following headers
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../src/vendor/autoload.php';

$app = new \Slim\App();


//endpoint for retrieval
$app->get('/phrases', function (Request $request, Response $response, array $args) {
    // Database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pronalysis";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $error = array("status" => "error", "message" => "Connection failed: " . $conn->connect_error);
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // Get pagination parameters from the request URL
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 25;  // Adjust the limit as needed
    $offset = ($page - 1) * $limit;

    // Fetch data from the phrases table
    $sql = "SELECT * FROM phrases LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    if ($result === false) {
        $error = array("status" => "error", "message" => "Query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    $data = array();

    while ($row = $result->fetch_assoc()) {
        $rowData = array(
            "id" => $row["id"],
            "phrase" => $row["phrase"],
            "classification" => $row["classification"],
            "created_at" => $row["created_at"]
        );

        array_push($data, $rowData);
    }

    // Perform a separate count query
    $countQuery = "SELECT COUNT(*) as total FROM phrases";
    $countResult = $conn->query($countQuery);

    if ($countResult === false) {
        $error = array("status" => "error", "message" => "Count query failed: " . $conn->error);
        $response->getBody()->write(json_encode($error));
        $conn->close();
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

    // Fetch the total count
    $totalCount = $countResult->fetch_assoc()['total'];

    // Close the database connection
    $conn->close();

    // Prepare the response data
    $data_body = array("status" => "success", "total" => $totalCount, "data" => $data);

    // Write the response
    $response->getBody()->write(json_encode($data_body));

    // Set the response headers and status code
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});


$app->run();

