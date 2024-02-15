<?php

class Food extends SQL_Model 
{
    public string $id;
    public string $name;
    public string $brand;
    public int $servingSize;
    public string $servingSizeMeasurementType;
    public int $protein;
    public int $fat;
    public int $carbs;
    public int $fiber;
    public int $sugar;
    public int $sodium;


    public function __construct( DB $database, string $id = "")
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object)
    {
        $this->name                       = $object["name"];
        $this->brand                      = $object["brand"];
        $this->servingSize                = $object["servingSize"];
        $this->servingSizeMeasurementType = $object["servingSizeMeasurementType"];
        $this->protein                    = $object["protein"];
        $this->fat                        = $object["fat"];
        $this->carbs                      = $object["carbs"];
        $this->fiber                      = $object["fiber"];
        $this->sugar                      = $object["sugar"];
        $this->sodium                     = $object["sodium"];
    }

    public function ToArray(): array
    {
        return array(
            "id"                         => $this->id,
            "name"                       => $this->name,
            "brand"                      => $this->brand,
            "servingSize"                => $this->servingSize,
            "servingSizeMeasurementType" => $this->servingSizeMeasurementType,
            "protein"                    => $this->protein,
            "fat"                        => $this->fat,
            "carbs"                      => $this->carbs,
            "fiber"                      => $this->fiber,
            "sugar"                      => $this->sugar,
            "sodium"                     => $this->sodium
        );
    }

    public function SetTable(): void
    {
        $this->database->setTable("Foods");
    }


    public function Insert(): Response
    {
        $this->SetTable();
        $this->id = $this->database->generateGUID();
        return $this->database->create($this->ToArray());
    }

    public function Details(): Response
    {
        $this->SetTable();
        return $this->database->getDetails($this->id);
    }

    public function Update(): Response
    {
        $this->SetTable();
        return $this->database->update($this->ToArray());
    }

    public function Delete(): Response
    {
        $this->SetTable();
        return $this->database->delete($this->id);
    }
}

?>