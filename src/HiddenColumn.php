<?php

namespace DoctrineCRUD;
class HiddenColumn extends Column {
    public function addSelect(&$select){
        return $this;
    }
    public function getValue(){
        return null;
    }
}

?>
