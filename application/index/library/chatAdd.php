<?php

namespace app\index\library;

class chatAdd {

    private $_name;

    public function __construct($obj) {
        $class = new \ReflectionClass($obj); //反向映射类
        $this->_name = $class->newInstance(); //实例化类
    }

    public function add($data) {
        $this->_name->Add($data);
    }

    public function select($data) {
        return $this->_name->select($data);
    }

    public function search($data) {
        return $this->_name->search($data);
    }

    public function addUser($data) {
        $this->_name->addUser($data);
    }
    public function updateStatus($data){
        $this->_name->updateStatus($data);
    }

}
