<?php
class Exercise extends SQL_Model {
    public string $id;
    public string $name;
    public string $activity;
    public string $date;
    public int $weight;
    public int $sets;
    public int $reps;
    public string $feedback;


    public function __construct(string $id, DB $database)
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object) {
        $this->name     = $object["name"];
        $this->activity = $object["activity"];
        $this->date     = $object["date"];
        $this->weight   = $object["weight"];
        $this->sets     = $object["sets"];
        $this->reps     = $object["reps"];
        $this->feedback = $object["feedback"];
    }


    public function ToArray(): array {
        return array(
            "id"       => $this->id,
            "name"     => $this->name,
            "activity" => $this->activity,
            "date"     => $this->date,
            "weight"   => $this->weight,
            "sets"     => $this->sets,
            "reps"     => $this->reps,
            "feedback" => $this->feedback
        );
    }

    public function SetTable(): void
    {
        $this->database->setTable("Exercises");
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