<?php

namespace DoctrineCRUD;
class ColumnRadio extends ColumnInput{
	public function render(){
		return sprintf('<td>
		<input type="radio" ng-click="change(1,\'%s\',item.%s,\'%s\',%s)" class="" ng-model="item.%s" name="%s"
                        ng-checked="item.%s == true"
                />
		 
		</td>',
                // $this->getAlias(true),
                // $this->getAlias(true),
                $this->property,
                $this->identity,
                str_replace('\\','_',$this->entity),
                (int)$this->confirm,
                $this->getAlias(true),
                $this->getAlias(true),
                $this->getAlias(true)
        );
	}
}