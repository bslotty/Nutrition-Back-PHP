<?php

class Meal extends SQL_Model
{
    public string $id;
    public string $name;
    public string $date;
    public array $parts; // Part[]

    public function __construct(string $id, DB $database)
    {
        parent::__construct($database);
        $this->id = $id;
        $this->parts = [];
    }

    /**
     * Conversions
     */
    public function ConvertFromClientRequest(array $object)
    {
        $this->name = $object["name"];
        $this->date = $object["date"];

        //  Parts
        require_once('./models/part.php');
        foreach ($object["parts"] as $part) {
            $p = new Part($this->database, $part["id"]);
            $p->ConvertFromClientRequest($part, $this, "meal");
            $this->parts[] = $p;
        }

    }

    public function ToArray(): array
    {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "date" => $this->date,
            "parts" => $this->PartsToArray()
        );
    }

    public function PartsToArray(): array
    {
        $a = [];

        foreach ($this->parts as $part) {
            $a[] = $part->ToArray();
        }

        return $a;
    }

    public function GetRelated(): Meal
    {
        // Query Parts table with parent_id and parent_type filters
        $this->database->setTable("Parts");
        $query = "SELECT * FROM `Parts` WHERE `parent_id`=:parent_id AND `parent_type`=:parent_type";
        $values = array(
            ":parent_id" => $this->id,
            ":parent_type" => "meal"
        );
        $parts = $this->database->Query($query, $values);

        if ($parts->success) {
            require_once("./models/part.php");
            $this->parts = $parts->data;
            foreach ($this->parts as $pi => $p) {
                $part = new Part($this->database, $p["id"]);
                $part->ConvertFromDB($p, "meal");
                $this->parts[$pi] = $part;
            }
        }

        return $this;
    }



    /**
     *  Specific DB Queries
     */
    public function Insert(): Response
    {
        //  Verify Object Properties?

        //  Base
        $this->database->setTable("Meals");
        $this->id = $this->database->generateGUID();
        $this->database->create($this->ToArray());

        //  Related
        foreach ($this->parts as $pi => $part) {
            $part->parent_id = $this->id;
            $part_response = $part->Insert();

            if (!$part_response->success) {
                $response = $part_response;
                break;
            }
        }

        $response = new Response(true);
        $response->data[] = $this->ToArray();

        return $response;
    }



    public function Update(): Response
    {
        $this->database->setTable("Meals");

        //  Base Update
        $response = $this->database->update($this->ToArray());
        if (!$response->success) {
            return $response;
        }

        //  Delete Related - delete from Parts table where parent_id = this meal and parent_type = 'meal'
        $this->database->setTable("Parts");
        $query = "DELETE FROM `Parts` WHERE `parent_id`=:parent_id AND `parent_type`=:parent_type";
        $values = array(
            ":parent_id" => $this->id,
            ":parent_type" => "meal"
        );
        $parts_response = $this->database->Query($query, $values);
        if (!$parts_response->success) {
            return $parts_response;
        }

        //  Create New
        $this->database->setTable("Parts");
        foreach ($this->parts as $pi => $part) {
            $part_response = $part->Insert();
            if (!$part_response->success) {
                $response->setStatus(false);
                $response->setMessage("Unable to create Part");
                break;
            }
        }

        return $response;
    }

    public function Delete(): Response
    {
        //  Delete Related - delete from Parts table where parent_id = this meal and parent_type = 'meal'
        $this->database->setTable("Parts");
        $query = "DELETE FROM `Parts` WHERE `parent_id`=:parent_id AND `parent_type`=:parent_type";
        $values = array(
            ":parent_id" => $this->id,
            ":parent_type" => "meal"
        );
        $parts_response = $this->database->Query($query, $values);
        if (!$parts_response->success) {
            return $parts_response;
        }

        //  Delete Meal
        $this->database->setTable("Meals");
        $response = $this->database->delete($this->id);
        return $response;
    }
}



?>