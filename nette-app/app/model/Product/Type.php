<?php

namespace App\Model\Product;

class Type {
    private $id;
    private $name;
    private $serialNumberGeneratorOptions;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getSerialNumberGeneratorOptions() {
        return $this->serialNumberGeneratorOptions;
    }
    
    public function setSerialNumberGeneratorOptions(SerialNumber\Generator\Options $options) {
        $this->serialNumberGeneratorOptions = $options;
    }
    
}
