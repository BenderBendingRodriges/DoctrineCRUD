<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Applet
 *
 * @author Kirill
 */
namespace DoctrineCRUD\Helper;


use \Zend\View\Helper\AbstractHelper;
class BodyColumn extends AbstractHelper{
    private $sm;
    public function __construct($sm) {
        
        $this->sm = $sm;
    }
    public function __invoke($column = null,$item = null,$angular = false) {
        if($column == null)return $this;
        $value = isset($item[$column->alias]) ? $item[$column->alias] : null;
        die();
        
        
        if($column->partial){
            $value = $item[0];
//            return '<td>' . $value . '</td>';
        }
        if($callable = $column->callable){
            $value = $callable($value,$item);
            return '<td>' . $value . '</td>';
        }
        if($value instanceof \DateTime){
            $value = $value->format($column->dataFormat);        
        }
        if(isset($column->affix)){
            $value = $value. $column->affix;
        }
        if($column->yesno){
            $yn = $this->sm->get('yesNo');
            $value =  $yn($value);
        }
        if($column->null && $value == null){
//            $yn = $this->sm->get('yesNo');
            $value =  $column->null;
        }
        if($column->helper){
           $h = $this->sm->get($column->helper);
           $value =  $h($value);
           
        }
        return '<td>' . $value . '</td>';
    }
    public function angular($column){
        if($column instanceof \Table\ColumnInput){
            return $column->render();
            
        }



        if($column->ng) return '<td>' . $column->ng . '</td>';
        return '<td><div ng-bind-html="item.' . $column->getAlias(true) . ' | unsafe"></div></td>';
    }
}

?>


