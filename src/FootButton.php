<?php

namespace DoctrineCRUD;
class FootButton {
    /** @var \Table\Table   */
    private $table;
    private $url;
    private $btn;
    private $title;
    private $dropdown;
    private $class = array('btn', 'btn-labeled','btn-block','btn-flat');
    private $icon = array('fa fa-fw fa-lg');
    
    public function setBtn($val){
        $this->btn = $val;
        $this->class[] = 'btn-'.$val;
    }
    public function setFa($val){
        $this->icon[] = 'fa-'.$val;
    }
    public function setClass($val){
        $this->class = array_merge($this->class,$val);
    }
    public function setIcon($val){
        $this->icon = array_merge($this->icon,$val);
    }

    public function __construct($options,$table) {
        $this->table = $table;
        foreach($options as $name => $value)
        {
            $setter = 'set'.ucfirst($name);
            if(method_exists($this, $setter))
                $this->$setter($value);
            else
                $this->$name = $value;
        }
    }
    
    public function __get($name) {
        $getter = 'get' .ucfirst($name);
        return method_exists($this, $getter) ? $this->$getter() : $this->$name;
    }
}

?>