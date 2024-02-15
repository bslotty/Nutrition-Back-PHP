<?php

require_once('response.php');

class DB
{
    protected $host;
    protected $db;
    protected $user;
    protected $pass;
    protected $charset;
    protected $dsn;

    public $stmt;
    public $pdo;
    public $data;
    public $fields;
    public $table;


    //  Init
    public function __construct($table_name)
    {
        $this->getConnection();
        $this->setTable($table_name);
    }

    // get the database connection
    public function getConnection(): void
    {
        $this->connection = null;

        require_once("db_auth.php");
        $this->host = $DB_HOST_URL;
        $this->username = $DB_USERNAME;
        $this->password = $DB_PASSWORD;
        $this->database = $DB_NAME;

        $this->charset = 'utf8mb4';
        $this->dsn = "mysql:host=" . $this->host . ";dbname=" . $this->database . ";charset=" . $this->charset . ";";

        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Error: " . $exception->getMessage();
        }
    }


    //  Generate GUID
    public function generateGUID(): string
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        } else {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    }


    //	Easy Return Last ID
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }



    //  Run Query and Return Data
    public function Query($q, $v): Response
    {
        $response = new Response();
        if (!$this->stmt = $this->pdo->prepare($q)) {
            $response->setStatus(false);
            $response->setMessage("Query Error");
        } else if (!$this->stmt->execute($v)) {
            $response->setStatus(false);
            $error = $this->stmt->errorInfo();
            $message = "$error[0]: $error[1] $error[2]";
            $response->setMessage($message);
        } else {
            $response->setStatus(true);
            $response->setResults($this->stmt);
        }

        //  Debug 
        $response->debug = array(
            "q" => $q,
            "v" => $v,
            "f" => $this->fields
        );

        return $response;
    }


    //  Return column names for SQL Queries
    public function getColumns($table_name)
    {
        $query = "DESCRIBE `" . $table_name . "`";
        $values = array();
        return $this->Query($query, $values);
    }



    public function setTable($table_name)
    {
        if ($table_name == $this->table) return;

        $fields = $this->getColumns($table_name);

        if ($fields->success) {
            $names = array();
            foreach ($fields->data as $row) {
                $names[] = $row["Field"];
            }

            $this->fields = $names;
            $this->table = $table_name;
            return $fields;
        } else {
            printf("Unable to get Columns from {$table_name}");
            http_response_code(501);
            exit();
        }
    }




    //  CRUD
    public function getList($start = 0, $limit = 500, $filter = null)
    {

        $query = "SELECT ";

        foreach ($this->fields as $column_name) {
            $query .= '`' . $column_name . '`,';
        }

        $query = substr($query, 0, -1);

        $query .= "
            FROM `" . $this->table . "`
            LIMIT " . (int) $start . ", " . (int) $limit . ";";


        //  Where
        if ($filter != null) {
            $query .= ``;
        }

        $values = array();

        return $this->Query($query, $values);
    }



    public function getDetails($id)
    {
        $query = "SELECT ";

        foreach ($this->fields as $column_name) {
            $query .= '`' . $column_name . '`,';
        }
        ;

        $query = substr($query, 0, -1);

        $query .= "
            FROM `" . $this->table . "`
            WHERE `id`=:id LIMIT 1";

        $values = array(
            ":id" => $id
        );

        return $this->Query($query, $values);
    }

    public function create($payload)
    {

        $values = array();

        $query = "INSERT INTO `" . $this->table . "` ";
        $column_str = "(";
        $values_str = "(";
        foreach ($this->fields as $key => $column_name) {
            if (isset($payload[$column_name])) {
                $column_str .= '`' . $column_name . '`, ';
                $values_str .= ':' . $column_name . ', ';

                $values[':' . $column_name] = $payload[$column_name];
            }
        }
        $column_str = substr($column_str, 0, -2) . ") VALUES ";
        $values_str = substr($values_str, 0, -2) . ");";

        $query .= $column_str . $values_str;

        return $this->Query($query, $values);
    }

    public function update($payload)
    {

        $values = array();

        $query = "UPDATE `" . $this->table . "` SET ";
        foreach ($this->fields as $key => $column_name) {
            if (isset($payload[$column_name])) {
                $query .= '`' . $column_name . '`=:' . $column_name . ',';
                $values[':' . $column_name] = $payload[$column_name];
            }
        }

        $query = substr($query, 0, -1);

        $query .= " WHERE `id`=:id LIMIT 1";

        return $this->Query($query, $values);
    }


    public function delete($id)
    {
        $query = "DELETE
            FROM `" . $this->table . "`
            WHERE `id`=:id";

        $values = array(
            ":id" => $id
        );

        return $this->Query($query, $values);
    }

    public function truncate()
    {
        $query = "TRUNCATE TABLE `" . $this->table . "`";

        return $this->Query($query, []);
    }





    //  ********************************
    //  Specific Queries
    //  ********************************

    public function getListForEmail($email)
    {


        $query = "SELECT `plan`.* , 
        `perm`.`access` AS `perm_access`, 
        `perm`.`id`     AS `perm_id`, 
        `perm`.`email`  AS `perm_email`,
        `temp`.`id`     AS `template_id`,
        `temp`.`url`    AS `template_url`
            FROM servicecatalog.spill_response_plans        AS `plan` 
            JOIN  servicecatalog.spill_response_permissions AS `perm` ON `perm`.`plan_id` = `plan`.`id`
            JOIN  servicecatalog.spill_response_templates   AS `temp` ON `temp`.`plan_id` = `plan`.`id`
            WHERE `perm`.`email` = :email";

        $values = array(
            ":email" => $email
        );

        return $this->Query($query, $values);
    }




    public function getRelated($table, $field, $id)
    {
        $query = "SELECT * FROM `{$table}` WHERE `{$field}`=:id";

        $values = array(
            ":id" => $id
        );

        return $this->Query($query, $values);
    }

    public function deleteRelated($table, $field, $id)
    {
        $query = "DELETE FROM `{$table}` WHERE `{$field}`=:id";

        $values = array(
            ":id" => $id
        );

        return $this->Query($query, $values);
    }
}
