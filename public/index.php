<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\Models\DB;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Hello World!');
    return $response;
});

$app->delete('/user-form/{id}', function ($request, $response, array $args) {
    $user_id = $args['id'];
    try {
        if($user_id != "") {
            $sql = "DELETE FROM userform where id=". $user_id;
            $db = new Db();
            $conn = $db->connect();
            $stmt = $conn->prepare($sql);
            $res = $stmt->execute();
            $db = null;

            $response->getBody()->write(json_encode($res));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(204);
        }
        else {
            $error = array(
                "message" => "Id not found!"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->get('/user-form/all', function (Request $request, Response $response) {
    $sql = "SELECT * FROM userform";
    $q_param = $request->getQueryParams();
    if($q_param["district"] && $q_param["district"] != ""){
        $sql = "SELECT * FROM userform where district='".$q_param["district"]."'";
    }
    try {
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $user_data = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($user_data));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->post('/user-form/add', function (Request $request, Response $response) {
    $userdata = $request->getParsedBody();
    $image = $userdata["image"];
    $firstname = $userdata["firstname"];
    $lastname = $userdata["lastname"];
    $district = $userdata["district"];
    $designation = $userdata["designation"];
    $phonenumber = $userdata["phonenumber"];
    $address = $userdata["address"];

    $resp_body = array("result"=> "Something went wrong!");
    $resp_code = 400;

    $sql = "INSERT INTO userform (firstname, lastname, district, designation, phonenumber, address, image) 
            VALUES (:firstname, :lastname, :district, :designation, :phonenumber, :address, :image)";

    try {
        $db = new Db();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':designation', $designation);
        $stmt->bindParam(':phonenumber', $phonenumber);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':image', $image);

        $res = $stmt->execute();
        $db = null;
        if($res) {
            $resp_body = array("result" => "Success!");
            $resp_code = 200;
        }
        $response->getBody()->write(json_encode($resp_body));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus($resp_code);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->run();