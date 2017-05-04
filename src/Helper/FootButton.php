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
class FootButton extends AbstractHelper{
    private $sm;
    public function __construct($sm) {
        $this->sm = $sm;
    }
    public function __invoke($btn,$tooltip = true) {
        //$btn->class = 'btn-labaled-collapsible';
    	// if($tooltip){
    	// 	$html = '<a href="%s" class="%s" title="%s" data-toggle="tooltip"><span class="btn-label"><i class="%s"></i></span> <span class="btn-labaled-collapse">%s</span></a>';        
     //    	return sprintf($html,$btn->url,implode(" ",$btn->class),$btn->title,implode(" ",$btn->icon),$btn->title);	
    	// }

        $html = '<a href="%s" class="%s" title="%s"><span class="btn-label"><i class="%s"></i></span> <span class="">%s</span></a>';        
        return sprintf($html,$btn->url,implode(" ",$btn->class),$btn->title,implode(" ",$btn->icon),$btn->title);
    }
}

?>
