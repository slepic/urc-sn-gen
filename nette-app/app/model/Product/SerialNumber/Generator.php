<?php

namespace App\Model\Product\SerialNumber;

class Generator {
    
    /**
     * @param string $originalSN
     * @param \App\Model\Product\SerialNumber\Generator\Options $options
     * @return string
     */
    public function generateFirst($originalSN, Generator\Options $options) {
        $offset = $options->getVariableCharOffset();
        $char = $options->getStartChar();
        return \substr($originalSN, 0, $offset)
               . $char[0]
               . \substr($originalSN, $offset + 1);
    }
    
    /**
     * @param string $lastSN
     * @param \App\Model\Product\SerialNumber\Generator\Options $options
     * @return string|false
     */
    public function generateNext($lastSN, Generator\Options $options) {
        $offset = $options->getVariableCharOffset();
        $char = ord($lastSN[$offset]);
        $end = ord($options->getEndChar()[0]);
        if($char === $end) {
            return false;
        }
        $start = ord($options->getStartChar()[0]);
        if($start < $end) {
            $char++; 
        } else {
            $char--;
        }
        return \substr($lastSN, 0, $offset)
               . chr($char)
               . \substr($lastSN, $offset + 1);
    }
}
