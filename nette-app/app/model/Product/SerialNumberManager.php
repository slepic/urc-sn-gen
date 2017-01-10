<?php

namespace App\Model\Product;

class SerialNumberManager {
    
    /**
     * @var \Nette\Database\Context
     */
    private $database;
    
    private $tableName = 'seriova_cisla';
    
    private $columnId = 'id';
    
    private $columnProductId = 'id_komponenty';
    
    private $columnSerialNumber = 'seriove_cislo';
    
    private $columnNote = 'poznamka';
    
    private $columnDateTimeInserted = 'datum';
    
    private $dateTimeFormat = 'Y-m-d H:i:s';
    
    public function __construct(\Nette\Database\Context $database) {
        $this->database = $database;
    }
    
    /**
     * @param int[] $productIds
     * @return \App\Model\Product\SerialNumber[]
     */
    public function getSerialNumbersByProductIds(array $productIds) {
        if(empty($productIds)) {
            return array();
        }
        $rows = $this->database->table($this->tableName)
                ->where($this->columnProductId, $productIds)
                ->order($this->columnId)
                ->fetchAll();
        return $this->mapRowsToModel($rows);
    }
    
    /**
     * @param int $productIds
     * @return \App\Model\Product\SerialNumber[]
     */
    public function getSerialNumbersByProductId($productId) {
        $rows = $this->database->table($this->tableName)
                ->where($this->columnProductId, $productId)
                ->order($this->columnId)
                ->fetchAll();
        return $this->mapRowsToModel($rows);
    }
    
    /**
     * @param string $serialNumber
     * @return \App\Model\Product\SerialNumber|null
     */
    public function getBySerialNumber($serialNumber) {
        if(!\is_string($serialNumber)) {
            throw new \InvalidArgumentException();
        }
        $row = $this->database->table(($this->tableName))
                ->where($this->columnSerialNumber, $serialNumber)
                ->fetch();
        if($row) {
            return $this->mapRowToModel($row);
        }
        return null;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow[] $rows
     * @return \App\Model\Product\SerialNumber[]
     */
    private function mapRowsToModel(array $rows) {
        $result = array();
        foreach($rows as $row) {
            $model = $this->mapRowToModel($row);
            $result[$model->getId()] = $model;
        }
        return $result;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow $row
     * @return \App\Model\Product\SerialNumber
     */
    private function mapRowToModel(\Nette\Database\Table\ActiveRow $row) {
       $model = new SerialNumber();
       $model->setId($row[$this->columnId]);
       $model->setProductId($row[$this->columnProductId]);
       $model->setSerialNumber($row[$this->columnSerialNumber]);
       $model->setNote($row[$this->columnNote]);
       $model->setDateTimeInserted(\DateTime::createFromFormat($this->dateTimeFormat, $row[$this->columnDateTimeInserted]));
       return $model;
    }
    
    public function insertSerialNumber(SerialNumber $serialNumber) {
        $data = array();
        $data[$this->columnProductId] = $serialNumber->getProductId();
        $data[$this->columnSerialNumber] = $serialNumber->getSerialNumber();
        $data[$this->columnNote] = $serialNumber->getNote();
        $data[$this->columnDateTimeInserted] = $serialNumber->getDateTimeInserted()->format($this->dateTimeFormat);
        $row = $this->database->table($this->tableName)->insert($data);
        $serialNumber->setId($row[$this->columnId]);
    }

    /**
     * @param string $serialNumber
     * @return boolean
     */
    public function existsSerialNumber($serialNumber) {
        $row = $this->database->table($this->tableName)
               ->where($this->columnSerialNumber, $serialNumber)
               ->fetch();
        return !!$row;
    }
    
    /**
     * @param \App\Model\Product\SerialNumber $serialNumber
     * @return boolean
     */
    public function deleteSerialNumber(SerialNumber $serialNumber) {
        return $this->deleteSerialNumberById($serialNumber->getId());
    }
    
    /**
     * @param int $id
     * @return boolean
     */
    public function deleteSerialNumberById($id) {
        if(!\is_int($id)) {
            throw new \InvalidArgumentException('expected int');
        }
        $num = $this->database->table($this->tableName)
                ->where($this->columnId, $id)
                ->delete();
        return $num === 1;
    }
    
    /**
     * @param int $productId
     * @return int Number of deleted entries
     */
    public function deleteSerialNumbersByProductId($productId) {
        if(!\is_int($productId)) {
            throw new \InvalidArgumentException('expected int');
        }
        return $this->database->table($this->tableName)
                ->where($this->columnProductId, $productId)
                ->delete();
    }
}
