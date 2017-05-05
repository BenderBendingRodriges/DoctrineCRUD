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
        if($btn->dropdown)
            return $this->dropdown($btn);
        $html = '<a href="%s" class="%s" title="%s"><span class="btn-label"><i class="%s"></i></span> <span class="">%s</span></a>';        
        return sprintf($html,$btn->url,implode(" ",$btn->class),$btn->title,implode(" ",$btn->icon),$btn->title);
    }


    public function dropdown($btn){
        $class = array_filter($btn->class,function($c){ return $c != 'btn-block';});
        $classC = array_filter($class,function($c){ return $c != 'btn-labeled';});
        $classC[] = 'dropdown-toggle';
        // die(var_dump($classC));
        $html = '
        <div class="btn-group">
            <button class="%s" title="%s"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="btn-label"><i class="%s"></i></span><span class="">%s</span></button>
            <button type="button" class="%s" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" style="padding: 0;min-width: 100%%;margin: 0; border: 0;">
                %s
            </ul>
        </div>';        
        $dropdown = "";

        foreach($btn->dropdown as $d)
            $dropdown .= '<a  style="margin: 0;text-align:left" class="text-left btn btn-success btn-block btn-flat" href="' . $d['url'] . '">' . $d['title'] . '</a>';

        return sprintf($html,implode(" ",$class),$btn->title,implode(" ",$btn->icon),$btn->title,implode(" ",$classC),$dropdown);

    }
}

?>
