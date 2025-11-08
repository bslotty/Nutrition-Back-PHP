<?php

class Food extends SQL_Model
{
    public string $id;
    public ?string $name;
    public ?string $brand;
    public ?int $servingSize;
    public ?string $servingUnit;

    // Macronutrients
    public ?int $protein;
    public ?int $fat;
    public ?int $carbs;
    public ?int $fiber;
    public ?int $sugar;
    public ?int $sodium;

    // Vitamins
    public ?int $vitaminA;
    public ?int $vitaminB1;
    public ?int $vitaminB2;
    public ?int $vitaminB3;
    public ?int $vitaminB5;
    public ?int $vitaminB6;
    public ?int $vitaminB7;
    public ?int $vitaminB9;
    public ?int $vitaminB12;
    public ?int $vitaminC;
    public ?int $vitaminD;
    public ?int $vitaminE;
    public ?int $vitaminK;

    // Minerals
    public ?int $calcium;
    public ?int $iron;
    public ?int $magnesium;
    public ?int $potassium;
    public ?int $zinc;


    public function __construct( DB $database, string $id = "")
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object)
    {
        $this->name        = $object["name"] ?? null;
        $this->brand       = $object["brand"] ?? null;
        $this->servingSize = $object["servingSize"] ?? null;
        $this->servingUnit = $object["servingUnit"] ?? null;

        // Macronutrients
        $this->protein = $object["protein"] ?? null;
        $this->fat     = $object["fat"] ?? null;
        $this->carbs   = $object["carbs"] ?? null;
        $this->fiber   = $object["fiber"] ?? null;
        $this->sugar   = $object["sugar"] ?? null;
        $this->sodium  = $object["sodium"] ?? null;

        // Vitamins
        $this->vitaminA   = $object["vitaminA"] ?? null;
        $this->vitaminB1  = $object["vitaminB1"] ?? null;
        $this->vitaminB2  = $object["vitaminB2"] ?? null;
        $this->vitaminB3  = $object["vitaminB3"] ?? null;
        $this->vitaminB5  = $object["vitaminB5"] ?? null;
        $this->vitaminB6  = $object["vitaminB6"] ?? null;
        $this->vitaminB7  = $object["vitaminB7"] ?? null;
        $this->vitaminB9  = $object["vitaminB9"] ?? null;
        $this->vitaminB12 = $object["vitaminB12"] ?? null;
        $this->vitaminC   = $object["vitaminC"] ?? null;
        $this->vitaminD   = $object["vitaminD"] ?? null;
        $this->vitaminE   = $object["vitaminE"] ?? null;
        $this->vitaminK   = $object["vitaminK"] ?? null;

        // Minerals
        $this->calcium   = $object["calcium"] ?? null;
        $this->iron      = $object["iron"] ?? null;
        $this->magnesium = $object["magnesium"] ?? null;
        $this->potassium = $object["potassium"] ?? null;
        $this->zinc      = $object["zinc"] ?? null;
    }

    public function ToArray(): array
    {
        return array(
            "id"          => $this->id,
            "name"        => $this->name,
            "brand"       => $this->brand,
            "type"        => "simple",
            "servingSize" => $this->servingSize,
            "servingUnit" => $this->servingUnit,

            // Macronutrients
            "protein" => $this->protein,
            "fat"     => $this->fat,
            "carbs"   => $this->carbs,
            "fiber"   => $this->fiber,
            "sugar"   => $this->sugar,
            "sodium"  => $this->sodium,

            // Vitamins
            "vitaminA"   => $this->vitaminA,
            "vitaminB1"  => $this->vitaminB1,
            "vitaminB2"  => $this->vitaminB2,
            "vitaminB3"  => $this->vitaminB3,
            "vitaminB5"  => $this->vitaminB5,
            "vitaminB6"  => $this->vitaminB6,
            "vitaminB7"  => $this->vitaminB7,
            "vitaminB9"  => $this->vitaminB9,
            "vitaminB12" => $this->vitaminB12,
            "vitaminC"   => $this->vitaminC,
            "vitaminD"   => $this->vitaminD,
            "vitaminE"   => $this->vitaminE,
            "vitaminK"   => $this->vitaminK,

            // Minerals
            "calcium"   => $this->calcium,
            "iron"      => $this->iron,
            "magnesium" => $this->magnesium,
            "potassium" => $this->potassium,
            "zinc"      => $this->zinc
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