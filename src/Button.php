<?php

namespace DoctrineCRUD;
class Button {
    /** @var \Table\Table   */
    private $table;
    private $url;
    private $urlReplace;
    private $title;
    private $if;
    private $class = array('btn','btn-circle');
    private $icon = array('fa fa-fw fa-lg');
    private $iconTxt;
    private $attr;

    public function getCondition(){
        return $this->if ? $this->if : 'true';
    }

    public function setBtn($val){
        $this->class[] = 'btn-'.$val;
    }
    public function setFa($val){
        $this->icon[] = 'fa-'.$val;
    }
    public function setClass($val){
        $this->class = array_merge($this->class,$val);
    }
    public function setUrl($val){
        preg_match_all('/__(.*?)__/s', $val, $matches);
        $this->urlReplace = array();
        foreach($matches[0] as $k =>$match){
            $sort = $matches[1][$k];
            $expolde = explode(".",$sort);
            $this->urlReplace[$match] =  array($sort,implode("_",explode(".",$sort)));
        }
        $this->url = $val;
    }
    public function getUrl($data){
        $url = $this->url;
        foreach($this->urlReplace as $replace => $with){
            $url = str_replace($replace, $data[$with[1]], $url);
        };
        
        return $url;
    }
    public function getUrlAngular(){
        $url = $this->url;
        foreach($this->urlReplace as $replace => $with){
//            die(var_dump($with));
            $url = str_replace($replace, '{{item.'.$with[1].'}}', $url);
        };
        
        return $url;
    }
    
    public function getSelects(){
        $selects = array();
        foreach ($this->urlReplace as $alias){
            $selects[] = $alias;
        }
        return $selects;
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