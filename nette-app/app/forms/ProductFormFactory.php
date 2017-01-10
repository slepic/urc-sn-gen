<?php

namespace App\Forms;

class ProductFormFactory {
    
    /**
     * @var \App\Model\Product\TypeManager
     */
    private $productTypeManager;
    
    /**
     * @var \App\Model\ProductManager
     */
    private $productManager;
    
    public function __construct(
            \App\Model\Product\TypeManager $productTypeManager,
            \App\Model\ProductManager $productManager) {
        $this->productTypeManager = $productTypeManager;
        $this->productManager = $productManager;
    }
    
    /**
     * @return \Nette\Application\UI\Form
     */
    public function createAddProductForm(callable $onSuccess)
    {
        $form = new \Nette\Application\UI\Form();
        $form->addProtection("The form has expired. Try again.");
        
        $this->addTypeSelect($form)
                ->setRequired("Zvolte typ komponenty!");
        
        
        $sn = $form->addText('sn', "SN")
                ->setRequired("Musíte zadat seriové číslo!");
                
        foreach($this->productTypeManager->getAllProductTypes() as $productType) {
            $regex = $productType->getSerialNumberGeneratorOptions()->getRegex();
            $sn->addConditionOn($form['type'], $form::EQUAL, $productType->getId())
                    ->addRule($form::PATTERN, "Seriové číslo musí být ve tvaru \"$regex\"!", '^' . $regex . '$');
        }
        
        $this->addSerialNumberNote($form);
        
        $button = $form->addSubmit('save', 'Přidat');
        $this->addButtonStyles($button, 'urc');
        
        $form->onSuccess[] = function ($form, $values) use ($onSuccess) {
            $type = $this->productTypeManager->getProductTypeById($values->type);
            try {
                $this->productManager->addProduct($type, $values->sn, $values->note);
            } catch (\App\Model\DuplicateSerialNumberException $e) {
                $form['sn']->addError("Seriové číslo již existuje!");
                return;
            }
            $onSuccess();
        };
        
        return $form;
    }
    
    public function createAddNextSerialNumberForm(\App\Model\Product $product, callable $onSuccess) {
        $form = new \Nette\Application\UI\Form();
        $form->addProtection("The form has expired. Try again.");
        
        $lastSN = $product->getLastSerialNumber()->getSerialNumber();
        $hidden = $form->addHidden('last_sn', $lastSN);
        $hidden->setRequired();
        $hidden->addRule($form::EQUAL, "Někdo Vás zřejmě předběhl.", $lastSN);
        
        $this->addSerialNumberNote($form);
        
        $button = $form->addSubmit('save', "Přidat");
        $this->addButtonStyles($button, 'urc');
        
        $form->onSuccess[] = function ($form, $values) use($product, $onSuccess) {
            $this->productManager->addProductSerialNumber($product, $values->note);
            $onSuccess();
        };
        
        return $form;
    }
    
    private function addSerialNumberNote(\Nette\Application\UI\Form $form) {
        $form->addTextArea('note', 'Poznámka')
                ->setRequired("Poznámka je povinná!");
    }
    
    /**
     * @param \Nette\Application\UI\Form $form
     * @return \Nette\Forms\Controls\SelectBox
     */
    private function addTypeSelect(\Nette\Application\UI\Form $form) {
        $options = array('' => " - ");
        $productTypes = $this->productTypeManager->getAllProductTypes();
        foreach($productTypes as $productType) {
            $options[$productType->getId()] = $productType->getName();
        }
        return $form->addSelect('type', 'Typ', $options);
    }
    
    /**
     * @return \Nette\Application\UI\Form
     */
    public function createProductFilterForm() {
        $form = new \Nette\Application\UI\Form();
        $form->setMethod('get');
        $this->addTypeSelect($form);
        $form->addText('sn', "Sériové číslo");
        $button = $form->addSubmit('filter', "Hledat");
        $this->addButtonStyles($button);
        return $form;
    }
    
    private function addButtonStyles($button, $extra = 'default') {
        $button->getControlPrototype()->setAttribute('class', 'btn btn-' . $extra);
    }
}
