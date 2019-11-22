<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');

require '../vendor/autoload.php';

$dbhost = 'localhost';
$dbport = '27017';
$dbname = 'budget_app';
$conn = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");


$app = new \Slim\App;

$app->get('/expenses', function (Request $request, Response $response, array $args) use($conn, $dbname) {

    $filter = [];
    $option = [];
    $read = new MongoDB\Driver\Query($filter, $option);
    
    $all_expenses = $conn->executeQuery("$dbname.expenses", $read);
    
    $expenses_arr = [];

    foreach ($all_expenses as $exp) {
        $expense = array(
            'opis' => $exp->opis,
            'iznos' => $exp->iznos
        );
        array_push($expenses_arr, $expense);
    }
    
    $response->getBody()->write(json_encode($expenses_arr));
    return $response;
});

$app->get('/incomes', function (Request $request, Response $response, array $args) use($conn, $dbname) {

    $filter = [];
    $option = [];
    $read = new MongoDB\Driver\Query($filter, $option);
    
    $all_incomes = $conn->executeQuery("$dbname.incomes", $read);
    
    $incomes_arr = [];

    foreach ($all_incomes as $inc) {
        $income = array(
            'opis' => $inc->opis,
            'iznos' => $inc->iznos
        );
        array_push($incomes_arr, $income);
    }
    
    $response->getBody()->write(json_encode($incomes_arr));
    return $response;
});


$app->post('/addexpense', function (Request $request, Response $response, array $args) use($conn, $dbname) {
    $req_body =  json_decode($request->getBody());

    $expense = array(
        'opis' => $req_body->opis,
        'iznos' => $req_body->iznos
    );
    
    $inserts = new MongoDB\Driver\BulkWrite();
    
    $inserts->insert($expense);

    $conn->executeBulkWrite("$dbname.expenses", $inserts);
    
    $response->getBody()->write('Successfully added expense: '.json_encode($req_body));
    
    return $response;
});


$app->post('/addincome', function (Request $request, Response $response, array $args) use($conn, $dbname) {
    $req_body =  json_decode($request->getBody());

    $income = array(
        'opis' => $req_body->opis,
        'iznos' => $req_body->iznos
    );
    
    $inserts = new MongoDB\Driver\BulkWrite();
    
    $inserts->insert($income);

    $conn->executeBulkWrite("$dbname.incomes", $inserts);
    
    $response->getBody()->write('Successfully added income: '.json_encode($req_body));
    
    return $response;
});



$app->run();