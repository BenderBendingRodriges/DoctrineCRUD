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
class BodyButton extends AbstractHelper{
    private $sm;
    public function __construct($sm) {
        $this->sm = $sm;
    }
    public function __invoke($btn = null,$data = null) {
        if($btn == null)return $this;
        $html = '<td style="width:1px;padding: 8px 2px;text-align:right"><a href="%s" class="%s" title="%s" data-toggle="tooltip"><i class="%s"></i>%s</a></td>';
        
        return sprintf($html,$btn->getUrl($data),implode(" ",$btn->class),$btn->title,implode(" ",$btn->icon),'');
    }
    
    public function angular($btn) {
        $html = '<td style="width:1px;padding: 8px 4px;text-align:right">
        <a %s ng-if="%s" ng-href="%s" class="%s" title="%s" data-toggle="tooltip" data-container="body"><i class="%s">%s</i>%s</a>
        <span ng-if="!(%s)"  class="btn btn-circle disabled btn-default" title="%s" data-toggle="tooltip" data-container="body" ><i class="%s">%s</i>%s</span>
        </td>';
        
        
        
        return sprintf($html,
            $btn->attr ? $btn->attr : '',
            $btn->getCondition(),
            $btn->getUrlAngular(),
            implode(" ",$btn->class),
            $btn->title,
            implode(" ",$btn->icon),
            $btn->iconTxt,
            '',

            $btn->getCondition(),
            // $btn->getUrlAngular(),
            // implode(" ",$btn->class),
            $btn->title,
            implode(" ",$btn->icon),
            $btn->iconTxt,
            ''
        );
    }
}

?>
