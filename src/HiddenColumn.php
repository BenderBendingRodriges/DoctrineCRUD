<?php

namespace DoctrineCRUD;
class HiddenColumn extends Column {
    public function addSelect(&$select){
        
        parent::addSelect($select);

        // die();
    }
    public function getValue(){
        return null;
    }
}

?>
