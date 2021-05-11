<?php

abstract class Model {
    protected $table;
    protected $mysqli;

    function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->mysqli->query($sql);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }
}
?>
