<?php

namespace App\Model;

class ProductFilter {
    private $typeId = null;
    private $serialNumber = "";
    private $start = 0;
    private $limit = null;
    
    public function getTypeId() {
        return $this->typeId;
    }
    
    public function setTypeId($id) {
        $this->typeId = $id;
    }
    
    public function getSerialNumber() {
        return $this->serialNumber;
    }
    
    public function setSerialNumber($sn) {
        $this->serialNumber = $sn;
    }
    
    public function getStart() {
        return $this->start;
    }
    
    public function setStart($start) {
        $this->start = $start;
    }
    
    public function getLimit() {
        return $this->limit;
    }
    
    public function setLimit($limit) {
        $this->limit = $limit;
        
    }
}
