<?php
class Weight extends SQL_Model  {
    public string $id;
    public string $date;
    public int $pounds;


    public function __construct(string $id, DB $database)
    {
        parent::__construct($database);
        $this->id = $id;
    }

    public function ConvertFromClientRequest(array $object) {
        $this->date   = $object["date"];
        $this->pounds = $object["pounds"];
    }


    public function ToArray(): array {
        return array(
            "id"     => $this->id,
            "date"   => $this->date,
            "pounds" => $this->pounds,
        );
    }
    public function SetTable(): void
    {
        $this->database->setTable("Weight");
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