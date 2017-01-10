<?php

namespace App\Model;

class Product {
    private $type;
    private $id;
    private $originalSerialNumber;
    private $nextSerialNumbers = array();
    
    /**
     * @return Product\Type
     */
    public function getType() {
        return $this->type;
    }
    
    public function setType(Product\Type $type) {
        $this->type = $type;
    }
    
    public function getTypeName() {
        return $this->type->getName();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getOriginalSerialNumber() {
        return $this->originalSerialNumber;
    }
    
    public function setOriginalSerialNumber(Product\SerialNumber $serialNumber) {
        $this->originalSerialNumber = $serialNumber;
    }
    
    public function getNextSerialNumbers() {
        return $this->nextSerialNumbers;
    }
    
    public function setNextSerialNumbers(array $serialNumbers) {
        $this->nextSerialNumbers = $serialNumbers;
    }
    
    public function addNextSerialNumber(Product\SerialNumber $serialNumber) {
        $this->nextSerialNumbers[] = $serialNumber;
    }
    
    /**
     * @return Product\SerialNumber
     */
    public function getLastSerialNumber() {
        return \end($this->nextSerialNumbers);
    }
}
