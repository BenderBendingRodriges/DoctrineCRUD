<?php

namespace DoctrineCRUD;
class HiddenColumn extends Column {
    public function addSelect(&$select){
        // return $this;
        parent::addSelect($select);
    }
    public function getValue(){
        return null;
    }
}

?>
