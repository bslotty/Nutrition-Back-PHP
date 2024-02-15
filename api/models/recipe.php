<?php

class Recipe extends SQL_Model  {
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


    public function __construct(string $id, DB $database)
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $o) {
        $this->name                       = $o["name"];
        $this->brand                      = $o["brand"];
        $this->servingSize                = $o["servingSize"];
        $this->servingSizeMeasurementType = $o["servingSizeMeasurementType"];
        $this->protein                    = $o["protein"];
        $this->fat                        = $o["fat"];
        $this->carbs                      = $o["carbs"];
        $this->fiber                      = $o["fiber"];
        $this->sugar                      = $o["sugar"];
        $this->sodium                     = $o["sodium"];
    }

    public function ToArray(): array {
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

    public function ConvertToClient(array $o){

    }


}

?>