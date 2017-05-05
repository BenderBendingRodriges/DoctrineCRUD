<?php

namespace DoctrineCRUD;
class ColumnInput extends Column{
	public function render(){
		// <i class="fa fa-check fa-fw fa-lg pull-right"></i>
		return sprintf('<td class="td-with-input">
			<i class="fa fa-pencil fa-2x"></i>
			<input type="text" ng-change="change(item.%s,\'%s\',item.%s,\'%s\',%s)" class="form-control" ng-model="item.%s"/></td>',
                $this->getAlias(true),
                $this->property,
                $this->identity,
                str_replace('\\','_',$this->entity),
                (int)$this->confirm,
                $this->getAlias(true)               
        );
	}
	
	protected $identity;
	protected $property;
	protected $entity;
	protected $confirm = false;
	public function __construct($options,$selectOptions,$table) {
		foreach($selectOptions as $propery => $value){
			if(property_exists($this, $propery))
				$this->{$propery} = $value;
		}

		parent::__construct($options,$table);
	}

	public static function create($type,$options,$selectOptions,$table){
		$class_name = "\Table\Column".ucfirst($type);
		if(class_exists($class_name))
			return new $class_name($options,$selectOptions,$table);
	}

}