<?php

namespace App\Presenters;

class ProductPresenter extends BasePresenter
{   
    /**
     * @var \App\Forms\ProductFormFactory
     */
    private $productFormFactory;
    
    /**
     * @var \App\Model\ProductManager
     */
    private $productManager;
        
    public function __construct(
            \App\Forms\ProductFormFactory $productFormFactory,
            \App\Model\ProductManager $productManager) {
        parent::__construct();
        $this->productFormFactory = $productFormFactory;
        $this->productManager = $productManager;
    }
    
    public function actionList($type = "", $sn = "", $page = 1) {
        if(!\is_numeric($page)) {
            $page = 1;
        } else {
            $page = (int) $page;
        }
        if($page < 1) {
            $page = 1;
        }
        $filter = new \App\Model\ProductFilter();
        $filter->setLimit(20);
        if(!$this['productFilterForm']->isSubmitted()) {
            $filter->setStart(($page - 1) * $filter->getLimit());
        }
        $filter->setSerialNumber($sn);
        if(\is_numeric($type)) {
            $filter->setTypeId((int) $type);
        }
        $this->loadListByFilter($filter);
        $this['productFilterForm']['type']->value = $filter->getTypeId();
        $this['productFilterForm']['sn']->value = $filter->getSerialNumber();
    }
    
    public function renderList() {
        $this->template->productAddForm = $this['productAddForm'];
        $this->template->productFilterForm = $this['productFilterForm'];
    }
    
    private function loadListByFilter(\App\Model\ProductFilter $filter) {
        $pager = new \Nette\Utils\Paginator();
        $this->template->products = $this->productManager->getProductsByFilter($filter);
        
        if($filter->getLimit() === null) {
            $pager->setItemCount(\count($this->template->products));
            $pager->setItemsPerPage($pager->getItemCount());
            $pager->setPage(1);
        } else {
            $pager->setItemCount($this->productManager->countProductsByFilter($filter));
            $pager->setItemsPerPage($filter->getLimit());
            $pager->setPage($filter->getStart() / $filter->getLimit() + 1);
        }
        $this->template->pager = $pager;
    }
    
    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentProductAddForm() {
        return $this->productFormFactory->createAddProductForm(function () {
            $this->flashMessage("Komponenta byla úspěšně vložena do databáze.");
            $this->redirect('Product:list');
        });
    }
    
    protected function createComponentProductFilterForm() {
        return $this->productFormFactory->createProductFilterForm();
    }
    
    public function actionDetail($id) {
        $product = $this->productManager->getProductById((int)$id);
        if(!$product) {
            $this->flashMessage("Product does not exist.", 'error');
            $this->redirect("Product:list");
        } else {
            $this->template->product = $product;
            $nextSerialNumber = $this->productManager->generateNextSerialNumber($product);
            $this->template->nextSerialNumber = $nextSerialNumber;
            if($nextSerialNumber) {
                $form = $this->productFormFactory->createAddNextSerialNumberForm($product, function () use ($product){
                    $this->flashMessage("Sériové číslo bylo úspěšně aktuálizováno.");
                    $this->redirect('Product:detail', array('id' => $product->getId()));
                });
                $this->addComponent($form, 'addNextSerialNumberForm');
                $this->template->addNextSerialNumberForm = $form;
            }
        }
    }
    
    public function actionDelete($id) {
        try {
            $this->productManager->deleteProductById((int) $id);
        } catch (\Exception $e) {
            $this->flashMessage('Došlo k neznámé chybě.', 'error');
            $this->redirect('Product:list');
            return;
        }
        $this->flashMessage('Komponenta byla úspešně smazána.');
        $this->redirect('Product:list');
    }
    
    public function actionDeleteLastSN($id) {
        try {
            $this->productManager->deleteLastSerialNumberByProductId((int) $id);
        } catch (\Exception $e) {
            $this->flashMessage('Došlo k neznámé chybě.', 'error');        
            $this->redirect('Product:detail', array('id' => $id));
            return;
        }
        $this->flashMessage('Seriové číslo bylo úspěšně odstraněno.');
        $this->redirect('Product:detail', array('id' => $id));
    }
}
