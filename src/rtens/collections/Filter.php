<?php
namespace rtens\collections;

interface Filter {

    const CLASSNAME = __CLASS__;

    /**
     * @abstract
     * @param mixed $element
     * @return boolean
     */
    public function matches($element);

}
