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
namespace Table\Helper;


use \Zend\View\Helper\AbstractHelper;
class HeaderColumn extends AbstractHelper{
    private $sm;
    private $params;
    public function __construct($sm) {
        $this->sm = $sm->getServiceLocator();
        $router = $this->sm->get('router');
        $request = $this->sm->get('request');
        $routeMatch = $router->match($request);
        $this->params = $routeMatch->getParams();
    }
    public function __invoke($column = null) {
        if($column == null)return $this;
        if($column->sort)
            return $this->getView()->render('helper/sorter.phtml',array('label'=>$column->label,'field'=>$column->alias,'params'=>$this->params));  
        return '<th>' . $column->label . '</th>';
    }

    public function angular($column){
        $label = $column->label;
        return $column->sort ? 
            sprintf('<th class="order-th" dt-criteria="%s"> <a href="javascript:void(0)">%s</th>',
                $column->alias ? $column->alias : $column->getField()[0],
                $label
            ) : '<th>' . $label . '</th>'

        ;
    }
    
}

?>
