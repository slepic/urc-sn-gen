<?php

namespace App\Model\Product;

class SerialNumber {
    private $id;
    private $productId;
    private $serialNumber;
    private $note;
    private $dateTimeInserted;
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getProductId() {
        return $this->productId;
    }
    
    public function setProductId($id) {
        $this->productId = $id;
    }
    
    public function getSerialNumber() {
        return $this->serialNumber;
    }
    
    public function setSerialNumber($sn) {
        $this->serialNumber = $sn;
    }
    
    public function getNote() {
        return $this->note;
    }
    
    public function setNote($note) {
        $this->note = $note;
    }
    
    public function getDateTimeInserted() {
        return $this->dateTimeInserted;
    }
    
    public function setDateTimeInserted(\DateTime $dateTime) {
        $this->dateTimeInserted = $dateTime;
    }
}
