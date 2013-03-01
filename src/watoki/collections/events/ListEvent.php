<?php
namespace watoki\collections\events;

class ListEvent extends CollectionEvent {

    public static $CLASSNAME = __CLASS__;

    private $index;

    function __construct($element, $index) {
        parent::__construct($element);
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getIndex() {
        return $this->index;
    }

}
