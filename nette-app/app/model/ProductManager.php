<?php

namespace App\Model;

class ProductManager {
    /**
     * @var \Nette\Database\Context
     */
    private $database;
    
    /**
     * @var \App\Model\Product\TypeManager
     */
    private $productTypeManager;
    
    /**
     * @var \App\Model\Product\SerialNumberManager
     */
    private $serialNumberManager;
    
    /**
     * @var \App\Model\Product\SerialNumber\Generator
     */
    private $serialNumberGenerator;
    
    /**
     * @var string
     */
    private $tableName = 'komponenty';
    
    /**
     * @var string
     */
    private $columnId = 'id';
    
    /**
     * @var string
     */
    private $columnProductTypeId = 'id_typ';
    
    /**
     * @var string
     */
    private $columnDateTimeChanged = 'datetime_change';
    
    
    public function __construct(
            \Nette\Database\Context $database,
            \App\Model\Product\TypeManager $productTypeManager,
            \App\Model\Product\SerialNumberManager $serialNumberManager,
            \App\Model\Product\SerialNumber\Generator $serialNumberGenerator) {
        $this->database = $database;
        $this->productTypeManager = $productTypeManager;
        $this->serialNumberManager = $serialNumberManager;
        $this->serialNumberGenerator = $serialNumberGenerator;
    }
    
    public function addProduct(Product\Type $productType, $originalSN, $note) {
        if($this->serialNumberManager->existsSerialNumber($originalSN)) {
            throw new DuplicateSerialNumberException();
        }
        $now = new \DateTime();
        $data = array();
        $data[$this->columnProductTypeId] = $productType->getId();
        $data[$this->columnDateTimeChanged] = $now->format('Y-m-d H:i:s');
                
        $origSN = new Product\SerialNumber();
        $origSN->setSerialNumber($originalSN);
        $origSN->setNote('');
        $origSN->setDateTimeInserted($now);
                
        $nextSN = new Product\SerialNumber();
        $nextSN->setSerialNumber($this->serialNumberGenerator->generateFirst($originalSN, $productType->getSerialNumberGeneratorOptions()));
        $nextSN->setNote($note);
        $nextSN->setDateTimeInserted($now);

        $this->database->beginTransaction();
        try {
            $row = $this->database->table($this->tableName)->insert($data);
            $product = $this->mapRowToModel($row);
            $origSN->setProductId($product->getId());
            $this->serialNumberManager->insertSerialNumber($origSN);
            $nextSN->setProductId($product->getId());
            $this->serialNumberManager->insertSerialNumber($nextSN);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
        $this->database->commit();
        
        $product->setOriginalSerialNumber($origSN);
        $product->setNextSerialNumbers(array($nextSN));
        
        return $product;
    }
    
    private function loadRelationsToModel(array $model) {
        $ids = \array_map(function ($m) {
            return $m->getId();
        }, $model);
        $allSerialNumbers = $this->serialNumberManager->getSerialNumbersByProductIds($ids);
        $collection = new Product\SerialNumber\Collection($allSerialNumbers);
        $serialNumbersByProductIds = $collection->splitByProductIds();
        foreach($model as $product) {
            $productSerialNumbers = $serialNumbersByProductIds[$product->getId()];
            $product->setOriginalSerialNumber(\array_shift($productSerialNumbers));
            $product->setNextSerialNumbers($productSerialNumbers);
        }
        return $model;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow[] $rows
     * @return \App\Model\Product[]
     */
    private function mapRowsToModel(array $rows) {
        $result = array();
        foreach($rows as $row) {
            $model = $this->mapRowToModel($row);
            $result[$model->getId()] = $model;
        }
        $this->loadRelationsToModel($result);
        return $result;
    }
    
    /**
     * @param \Nette\Database\Table\ActiveRow $row
     * @return \App\Model\Product
     */
    private function mapRowToModel(\Nette\Database\Table\ActiveRow $row) {
        $model = new Product();
        $model->setId($row[$this->columnId]);
        $type = $this->productTypeManager->getProductTypeById($row[$this->columnProductTypeId]);
        $model->setType($type);
        return $model;
    }

    /**
     * @param int $id
     * @return \App\Model\Product
     */
    public function getProductById($id) {
        $row = $this->database->table($this->tableName)
                ->where($this->columnId, $id)
                ->fetch();
        if(!$row) {
            return null;
        }
        $type = $this->productTypeManager->getProductTypeById($row[$this->columnProductTypeId]);
        $sns = $this->serialNumberManager->getSerialNumbersByProductId($id);
        $product = $this->mapRowToModel($row);
        $product->setType($type);
        $product->setOriginalSerialNumber(\array_shift($sns));
        $product->setNextSerialNumbers($sns);
        return $product;
    }
    
    /**
     * @param \App\Model\Product $product
     * @return string|false
     */
    public function generateNextSerialNumber(Product $product) {
        $lastSN = $product->getLastSerialNumber()->getSerialNumber();
        $options = $product->getType()->getSerialNumberGeneratorOptions();
        return $this->serialNumberGenerator->generateNext($lastSN, $options);
    }
    
    /**
     * @param \App\Model\Product $product
     * @param string $note
     */
    public function addProductSerialNumber(Product $product, $note) {
        if(!\is_int($product->getId())) {
            throw new \InvalidArgumentException();
        }
        $sn = $this->generateNextSerialNumber($product);
        if(!$sn) {
            throw new \Exception("Out of serial numbers.");
        }
        $snModel = new Product\SerialNumber();
        $snModel->setProductId($product->getId());
        $snModel->setSerialNumber($sn);
        $snModel->setNote($note);
        $snModel->setDateTimeInserted(new \DateTime());
        
        $data= array();
        $data[$this->columnDateTimeChanged] = $snModel->getDateTimeInserted()->format('Y-m-d H:i:s');
        
        $this->database->beginTransaction();
        try {
            $this->database->table($this->tableName)
                    ->where($this->columnId, $product->getId())
                    ->update($data);
            $this->serialNumberManager->insertSerialNumber($snModel);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
        $this->database->commit();
        
        $product->addNextSerialNumber($snModel);
    }
    
    public function deleteProductById($id) {
        if(!\is_int($id)) {
            throw new \InvalidArgumentException('expected int');
        }
        $this->database->beginTransaction();
        try {
            $this->serialNumberManager->deleteSerialNumbersByProductId($id);
            $this->database->table($this->tableName)
                    ->where($this->columnId, $id)
                    ->delete();
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
        $this->database->commit();
    }
    
    public function deleteLastSerialNumberByProductId($id) {
        if(!\is_int($id)) {
            throw new \InvalidArgumentException('expected int');
        }
        $product = $this->getProductById($id);
        if(!$product) {
            throw new UnknownProductException();
        }
        $nextSerials = $product->getNextSerialNumbers();
        if(\count($nextSerials) < 2) {
            throw new TooFewSerialNumbersException();
        }
        $sn = \array_pop($nextSerials);
        
        $data = array();
        $data[$this->columnDateTimeChanged] = $product->getLastSerialNumber()
                ->getDateTimeInserted()->format('Y-m-d H:i:s');
        
        $this->database->beginTransaction();
        try {
            $this->database->table($this->tableName)
                    ->where($this->columnId, $product->getId())
                    ->update($data);
            $this->serialNumberManager->deleteSerialNumber($sn);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
        $this->database->commit();
        
        $product->setNextSerialNumbers($nextSerials);
    }
    
    public function getProductsByFilter(\App\Model\ProductFilter $filter) {
        $table = $this->mapFilterToSelection($filter);
        if(!\is_object($table)) {
            return array();
        }
        if($filter->getLimit() !== null) {
            $table->limit($filter->getLimit(), $filter->getStart());
        }
        $table->order($this->columnDateTimeChanged . ' DESC');
        $rows = $table->fetchAll();
        return $this->mapRowsToModel($rows);
    }
    
    /**
     * @param \App\Model\ProductFilter $filter
     * @return int
     */
    public function countProductsByFilter(\App\Model\ProductFilter $filter) {
        $selection = $this->mapFilterToSelection($filter);
        if(!\is_object($selection)) {
            return 0;
        }
        return $selection->count();
    }
    
    /**
     * @param \App\Model\ProductFilter $filter
     * @return \Nette\Database\Table\Selection
     */
    private function mapFilterToSelection(\App\Model\ProductFilter $filter) {
        $table = $this->database->table(($this->tableName));
        if($filter->getTypeId() !== null) {
            $table = $table->where($this->columnProductTypeId, $filter->getTypeId());
        }
        if(\strlen($filter->getSerialNumber()) > 0) {
            $snModel = $this->serialNumberManager->getBySerialNumber($filter->getSerialNumber());
            if(!$snModel) {
                return null;
            }
            $table->where($this->columnId, $snModel->getProductId());
        }
        return $table;
    }
}
