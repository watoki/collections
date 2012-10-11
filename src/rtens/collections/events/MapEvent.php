<?php
namespace rtens\collections\events;

class MapEvent extends CollectionEvent {

    public static $CLASSNAME = __CLASS__;

    private $key;

    function __construct($key, $value) {
        parent::__construct($value);
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }

    public function getValue() {
        return $this->getElement();
    }

}
