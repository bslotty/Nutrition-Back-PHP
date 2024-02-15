<?php
class Part extends SQL_Model
{
    public string $id;
    public string $parent_id;
    public string $parent_type;
    public string $join_id;
    public string $join_type;
    public string $amount;
    public string $amount_type;

    public Food $food;


    public function __construct( DB $database, string $id = "")
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object, Meal $meal)
    {
        $this->parent_id   = $meal->id;
        $this->parent_type = "meal";
        $this->join_type   = "food";
        $this->join_id     = $object["join"]["id"];

        $this->GetRelated();


        $this->amount = $object["amount"];
    }

    public function ConvertFromDB(array $object)
    {
        $this->parent_id   = $object["meal_id"];
        $this->parent_type = "meal";
        $this->join_id     = $object["food_id"];
        $this->join_type   = "food";
        $this->amount      = $object["amount"];

        $this->GetRelated();
    }

    public function ToArray(): array
    {
        return array(
            "id"      => $this->id,
            "meal_id" => $this->parent_id,
            "join_id" => $this->join_id,
            "food_id" => $this->join_id,
            "food"    => $this->food->ToArray(),
            "amount"  => $this->amount,
        );
    }

    public function GetRelated(): void {
        require_once("./models/food.php");
        $food = new Food($this->database, $this->join_id);
        $f    = $this->database->getRelated("Foods", "id", $this->join_id);
        if ($f->success) {
            $food->ConvertFromClientRequest($f->data[0]);
        }

        $this->food = $food;
    }

    public function Insert(): Response
    {
        $this->database->setTable("Meal_Parts");
        $this->id = $this->database->generateGUID();
        return $this->database->create($this->ToArray());
    }

}