<?php
class SQL_Model
{
    protected DB $database;

    public function __construct(DB $database)
    {
        $this->database = $database;
    }

    public function getDatabase(): DB
    {
        return $this->database;
    }

    public function FormatFromClient($object, $type): object
    {
        switch ($type) {
            case "Foods":
                require_once('./models/food.php');
                $obj = new Food($this->database, $object["id"]);
                $obj->ConvertFromClientRequest($object);
                break;

            case "Exercises":
                require_once('./models/exercise.php');
                $obj = new Exercise($object["id"], $this->database);
                $obj->ConvertFromClientRequest($object);
                break;

            case "Weight":
                require_once('./models/weight.php');
                $obj = new Weight($object["id"], $this->database);
                $obj->ConvertFromClientRequest($object);
                break;

            case "Recipes":
                require_once('./models/recipe.php');
                $obj = new Recipe($object["id"], $this->database);
                $obj->ConvertFromClientRequest($object);
                break;

            case "Meals":
                require_once('./models/meal.php');
                $obj = new Meal($object["id"], $this->database);
                $obj->ConvertFromClientRequest($object);
                break;
        }

        if ($obj == null) {
            http_response_code(400);
        }

        return $obj;
    }
}
