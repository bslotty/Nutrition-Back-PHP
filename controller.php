<?php
//  Session
session_start();

//  Headers
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Max-Age: 1000');
header('Content-Type: application/json,text/plain');

//  Init Return
$return = array();

//  Get Method and Properties
$method = $_SERVER['REQUEST_METHOD'];
if ($method != "POST") {
    http_response_code(405);
}

//  Body
$payload = json_decode(file_get_contents('php://input'), TRUE);
$type = $payload['type'];
if ($type == null) {
    http_response_code(400);
}

$action = $payload['action'];
if ($action == null) {
    http_response_code(400);
}

//  DB
require_once('./sql.php');
$database = new DB($type);

//  SQL_Modeler
require_once('./models/sql_model.php');
$modeler = new SQL_Model($database);

//  Set ID on creation
if (isset($payload["object"])) {
    if (strpos(strtolower($payload["object"]["id"]), "create") != false) {
        $payload["object"]["id"] = $database->generateGUID();
    }
}

switch ($action) {
    case "list":
        $start = isset($payload["start"]) ? $payload["start"] : 0;
        $count = isset($payload["count"]) ? $payload["count"] : 10;
        $return[] = $database->getList($start, $count);

        switch (strtolower($type)) {
            case "meals":
                require_once("./models/meal.php");

                foreach ($return[0]->data as $i => $m) {
                    $meal = new Meal($m["id"], $modeler->database);
                    $meal->name = $m["name"];
                    $meal->date = $m["date"];

                    $meal->GetRelated();

                    $return[0]->data[$i] = $meal;
                }
                break;

            case "recipes":
                require_once("./models/recipe.php");

                foreach ($return[0]->data as $i => $r) {
                    $recipe = new Recipe($r["id"], $modeler->database);
                    $recipe->name = $r["name"];

                    $recipe->GetRelated();

                    $return[0]->data[$i] = $recipe;
                }
                break;

        }

        break;

    case "detail":
        $object = $modeler->FormatFromClient($payload['object'], $type);
        $return[] = $object->Details($database);
        break;

    case "create":
        $object = $modeler->FormatFromClient($payload['object'], $type);
        $return[] = $object->Insert($database);
        break;

    case "update":
        $object = $modeler->FormatFromClient($payload['object'], $type);
        $return[] = $object->Update($database);
        break;

    case "delete":
        $object = $modeler->FormatFromClient($payload['object'], $type);
        $return[] = $object->Delete($database);
        break;



    default:
        http_response_code(405);
        exit();
}


//  Return Response
printf(json_encode($return));