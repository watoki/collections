<?php
namespace rtens\collections\events;

class CollectionEvent {

    public static $CLASSNAME = __CLASS__;

    private $element;

    function __construct($element) {
        $this->element = $element;
    }

    public function getElement() {
        return $this->element;
    }

}
