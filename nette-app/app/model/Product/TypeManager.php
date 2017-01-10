<?php

namespace App\Model\Product;

class TypeManager {
    
    /**
     * @var \Nette\Database\Context
     */
    private $database;
    
    private $tableName = 'typy_komponent';
    
    private $columnId = 'id';
    
    private $columnName = 'nazev';
    
    private $columnSerialNumberRegex = 'sn_regular';
    
    private $columnSerialNumberVariableCharOffset = 'sn_var_offset';
    
    private $columnSerialNumberStartChar = 'sn_start_char';
    
    private $columnSerialNumberEndChar = 'sn_end_char';
    
    private $allProductTypes = null;
    
    public function __construct(\Nette\Database\Context $database) {
        $this->database = $database;
    }
    
    /**
     * @return \App\Model\Product\Type[]
     */
    public function getAllProductTypes() {
        if($this->allProductTypes === null) {
            $data = $this->database->table($this->tableName)->fetchAll();
            $this->allProductTypes = $this->mapRowsToModel($data);
        }
        return $this->allProductTypes;
    }
    
    /**
     * @param int $id
     * @return \App\Model\Product\Type
     */
    public function getProductTypeById($id) {
        $types = $this->getAllProductTypes();
        return isset($types[$id]) ? $types[$id] : null;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow[] $data
     * @return \App\Model\Product\Type[]
     */
    private function mapRowsToModel(array $data) {
        $result = array();
        foreach($data as $row) {
            $productType = $this->mapRowToModel($row);
            $result[$productType->getId()] = $productType;
        }
        return $result;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow $data
     * @return \App\Model\Product\Type
     */
    private function mapRowToModel(\Nette\Database\Table\ActiveRow $data) {
        $genOptions = new SerialNumber\Generator\Options();
        $genOptions->setRegex($data[$this->columnSerialNumberRegex]);
        $genOptions->setVariableCharOffset($data[$this->columnSerialNumberVariableCharOffset]);
        $genOptions->setStartChar($data[$this->columnSerialNumberStartChar]);
        $genOptions->setEndChar($data[$this->columnSerialNumberEndChar]);
        
        $productType = new Type();
        $productType->setId($data[$this->columnId]);
        $productType->setName($data[$this->columnName]);
        $productType->setSerialNumberGeneratorOptions($genOptions);
        return $productType;
    }
}
