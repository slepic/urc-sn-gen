<?php

namespace App\Model\Product\SerialNumber;

class Collection extends \ArrayObject {
    
    /**
     * @return \App\Model\Product\SerialNumber[][]
     */
    public function splitByProductIds() {
        $result = array();
        foreach($this as $sn) {
            $result[$sn->getProductId()][$sn->getId()] = $sn;
        }
        return $result;
    }
}
