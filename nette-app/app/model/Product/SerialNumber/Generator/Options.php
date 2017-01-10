<?php

namespace App\Model\Product\SerialNumber\Generator;

class Options {
    private $regex;
    private $variableCharOffset;
    private $startChar;
    private $endChar;
    
    /**
     * @return string
     */
    public function getRegex() {
        return $this->regex;
    }
    
    /**
     * @param string $regex
     */
    public function setRegex($regex) {
        $this->regex = $regex;
    }
    
    /**
     * @return int
     */
    public function getVariableCharOffset() {
        return $this->variableCharOffset;
    }
    
    /**
     * @param int $offset
     */
    public function setVariableCharOffset($offset) {
        $this->variableCharOffset = $offset;
    }
    
    /**
     * @return char
     */
    public function getStartChar() {
        return $this->startChar;
    }
    
    /**
     * @param char $char
     */
    public function setStartChar($char) {
        $this->startChar = $char;
    }
    
    /**
     * @return char
     */
    public function getEndChar() {
        return $this->endChar;
    }
    
    /**
     * @param char $char
     */
    public function setEndChar($char) {
        $this->endChar = $char;
    }
}
