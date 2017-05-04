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
class Table extends AbstractHelper{
    private $sm;
    private $params;
    public function __construct($sm) {
        $this->sm = $sm->getServiceLocator();
        $router = $this->sm->get('router');
        $request = $this->sm->get('request');
        $routeMatch = $router->match($request);
        $this->params = $routeMatch->getParams();
    }
    public function __invoke($table) {
        
        return $this->getView()->render('helper/table.phtml',array('table'=>$table->prepare($this->params)));
    }
}

?>
