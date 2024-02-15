<?php
class Response
{
    public bool $success;
    public string $message;
    public array $data;
    public int $affected_rows;
    public array $debug;


    //  Init
    public function __construct($success = false)
    {
        $this->success = $success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getStatus()
    {
        return $this->success;
    }

    public function setStatus($status)
    {
        $this->success = $status;
    }


    public function setResults($stmt)
    {
        $this->data          = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->affected_rows = $stmt->rowCount();
    }

    public function setDebug($query, $values, $fields)
    {
        $this->debug = array(
            "q" => $query,
            "v" => $values,
            "f" => $fields
        );
    }
}