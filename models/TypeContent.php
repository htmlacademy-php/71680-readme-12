<?php
require_once('Model.php');

class TypeContent extends Model {
    protected $table = 'type_contents';
    protected $id;

    function __construct(Mysqli $mysqli, int $id = null)
    {
        parent::__construct($mysqli);
        $this->id = $id;
    }
}
?>
