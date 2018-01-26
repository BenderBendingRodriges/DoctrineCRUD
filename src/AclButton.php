<?php

namespace DoctrineCRUD;
class AclButton extends Button {
    

    protected $acl;
    protected $resource;
    protected $privilage;



    public function __construct($options,$table,$acl,$resource,$privilage) {
        parent::__construct($options,$table);

        $this->acl = $acl;
        $this->resource = $resource;
        $this->privilage = $privilage;
    }



    public function toJSON($data){
        // die(var_dump())
        $url = $this->getUrl($data);
        // var_dump($this->url);
        // var_dump($url);
        // var_dump($data);
        $output = [
            'icon' => implode(" ",$this->icon),
            'class' => implode(" ",$this->class),
            'url' => null,
            'title' => $this->title,
            'allowed' => false,
        ];
        $this->acl->setUrl($url);
        $allowed = $this->acl->isAllowed(null,$this->resource,$this->privilage);
        $this->acl->clearUrl();

        if(!$allowed)
            return $output;


        $output['allowed'] = true;
        $output['url'] = $url;
        return $output;
    }
}

?>