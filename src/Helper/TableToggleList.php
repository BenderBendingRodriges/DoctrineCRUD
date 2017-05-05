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
class TableToggleList extends AbstractHelper{
	

	public function __construct($sm){}


	public function __invoke($value){
		$value = explode(";",$value);
		$value = array_filter($value);

		$list = '<ul style="margin:-8px"><li>' .  implode('</li><li>',$value) . '</li></ul>';
		if(!count($value))
			return '<div class="table-toggle-list text-center"><strong class=""><span class="badge">0</span></strong></div>';	
		return '<div class="table-toggle-list"><label><span class="badge bg-green">'.count($value).'</span></label>' . $list . "</div>";
	}

}?>
