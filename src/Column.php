<?php

namespace DoctrineCRUD;
class Column {
    /** @var \Table\Table   */
    private $table;
    private $label;
    private $field;
    private $alias;
    private $search = false;
    private $sort = true;
    private $partial = false;
    private $dateFormat = 'Y-m-d';
    private $function;
    private $null;
    private $yesno;
    private $ng;
    private $viewHelper = null;
    private $sfunction = null;
    private $price = null;
    private $thumb = null;
    private $helper = null;
    
    
    public function getCallable(){
        return $this->function && is_callable($this->function) ? $this->function : null;
    }


    public function addSelect(&$select){
        
        if(!$this->partial){
            list($field,$alias) = $this->getField();
            $select[$alias] = $field . ' as ' . $alias;
        }else{
            list($entity,$prop) = explode(".",$this->field);
//            die('partial '.$entity.'.{'.$prop.'}');
//            $select['partial '.$entity] = 'partial '.$entity.'.{'.$prop.'}';
            $this->table->partials[$entity] = array_merge(isset($this->table->partials[$entity]) ? $this->table->partials[$entity] : array(),array($prop));
        }
    }

    public function getAlias($force = false){
        return $this->partial && !$force ? $this->field : $this->alias;
    }

    public function __construct($options,$table) {
        $this->table = $table;
        foreach($options as $name => $value)
        {
            
            $name = explode("-",$name);
            $name = lcfirst(implode("",array_map(function($r){return ucfirst($r);},$name)));
            
            $setter = 'set'.ucfirst($name);
            if(method_exists($this, $setter)){
                $this->$setter($value);
            }
            else{
                $this->{$name} = $value;
                
                
            }

            

        }

    }
    
    public function getField(){
        $this->field =  strpos($this->field,'.') === FALSE ? $this->table->getFromAlias() . '.' . $this->field : $this->field;
        if(!$this->alias){
            $this->alias = str_replace(array('.','(',')'),array('_','_',''),$this->field);
        }
        return array($this->field , $this->alias);
    }
    public function __get($name) {
        $getter = 'get' .ucfirst($name);
        return method_exists($this, $getter) ? $this->$getter() : $this->$name;
    }
    
    
    public function getValue($item){
        $value = isset($item[$this->alias]) ? $item[$this->alias] : null;
        if($this->partial){
            $value = $item[0];
            if($f = $this->function)
                $value = $value->{$f};
            elseif($f = $this->sfunction){
                // $f = '$'.$f;
                $value = $value->$f;//$value->$f;
            }
        }
        if($callable = $this->callable){
            $value = $callable($value,$item);
            // return $value;
        }
        if($value instanceof \DateTime){
            $value = $value->format($this->dateFormat);        
        }
        
        if($this->price){
            $value = number_format($value,2,'.',' ') ;
        }
        if($this->yesno){
            $vhm = $this->table->getServiceManager()->get('ViewHelperManager');
            $helper = $vhm->get('yesNo');
            $value =  $helper($value);
        }
        if($this->null !== null && $value == null){
            $value =  $this->null;
        }
        if($this->thumb){
            $value =  sprintf('<img src="/thumb/%s/%s" class="img-thumb" alt="">',$this->thumb,$value);
        }

        if($this->viewHelper){
            $vhm = $this->table->getServiceManager()->get('ViewHelperManager');
            $helper = $vhm->get($this->viewHelper);
            $value = $helper($value);
        }
        if(isset($this->affix)){
            $value = $value. $this->affix;
        }
        return $value;
    }
}

?>
