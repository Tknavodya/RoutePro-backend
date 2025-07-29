<?php
class Controller {
    public function model($modelName) {
        require_once "../app/models/$modelName.php";
        return new $modelName();
    }
}
