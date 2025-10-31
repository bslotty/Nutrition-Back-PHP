<?php
class Part extends SQL_Model
{
    public string $id;
    public string $parent_id;
    public string $parent_type;
    public string $join_id;
    public string $join_type;
    public string $size;
    public string $unit;

    public Food $food;


    public function __construct(DB $database, string $id = "")
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object, $parent, string $parent_type)
    {
        $this->parent_id   = $parent->id;
        $this->parent_type = $parent_type;

        // Determine join based on object structure
        $join_object = null;
        if (isset($object["food"])) {
            $join_object = $object["food"];
        } else if (isset($object["join"])) {
            $join_object = $object["join"];
        }

        // Determine join_type from the joined object's type field
        if ($join_object && isset($join_object["type"])) {
            // If type is 'compound', it's a Recipe; if 'simple', it's a Food
            $this->join_type = ($join_object["type"] === "compound") ? "recipe" : "food";
        } else {
            // Default to food if type not specified
            $this->join_type = "food";
        }

        $this->join_id = $join_object["id"];

        // Set size (amount) - convert to string for storage
        $this->size = isset($object["amount"]) ? strval($object["amount"]) : "0";

        // Set unit - use part's unit if provided, otherwise fall back to food's serving unit
        if (isset($object["unit"]) && $object["unit"] !== "") {
            $this->unit = strval($object["unit"]);
        } else if (isset($join_object["serving"]["unit"])) {
            $this->unit = strval($join_object["serving"]["unit"]);
        } else {
            $this->unit = "";
        }

        $this->GetRelated();
    }

    public function ConvertFromDB(array $object, string $parent_type)
    {
        $this->parent_id   = $object["parent_id"];
        $this->parent_type = $object["parent_type"];
        $this->join_id     = $object["join_id"];
        $this->join_type   = isset($object["join_type"]) ? $object["join_type"] : "food";
        // Map database columns to internal properties
        $this->size        = isset($object["amount"]) ? $object["amount"] : "";
        $this->unit        = isset($object["amount_type"]) ? $object["amount_type"] : "";

        $this->GetRelated();
    }

    public function ToArray(bool $includeRelated = true): array
    {
        $array = array(
            "id"          => $this->id,
            "parent_id"   => $this->parent_id,
            "parent_type" => $this->parent_type,
            "join_id"     => $this->join_id,
            "join_type"   => $this->join_type,
            "amount"      => $this->size,      // Map size to database column 'amount'
            "amount_type" => $this->unit,      // Map unit to database column 'amount_type'
        );

        // Only include food data when not saving to DB (for display purposes)
        // The food object is not a database column
        if ($includeRelated && $this->food) {
            $array["food"] = $this->food->ToArray();
        }

        return $array;
    }

    public function GetRelated(): void
    {
        // Determine which table to query based on join_type
        if ($this->join_type === "recipe") {
            require_once("./models/recipe.php");
            $table = "Recipes";
            $join_object = new Recipe($this->database, $this->join_id);
        } else {
            require_once("./models/food.php");
            $table = "Foods";
            $join_object = new Food($this->database, $this->join_id);
        }

        $result = $this->database->getRelated($table, "id", $this->join_id);
        if ($result->success && isset($result->data[0])) {
            $join_object->ConvertFromClientRequest($result->data[0]);

            // If it's a recipe, also get its related parts
            if ($this->join_type === "recipe") {
                $join_object->GetRelated();
            }
        }

        $this->food = $join_object;
    }

    public function Insert(): Response
    {
        $this->database->setTable("Parts");
        $this->id = $this->database->generateGUID();
        // Pass false to exclude related objects (food) - only save database columns
        return $this->database->create($this->ToArray(false));
    }

}