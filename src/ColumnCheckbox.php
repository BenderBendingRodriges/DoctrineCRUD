<?php

namespace DoctrineCRUD;
class ColumnCheckbox extends ColumnInput{
	public function render(){
		return sprintf('<td class="text-center no-padding" style="    width: 40px;" >
                <input type="checkbox"  ng-change="change(item.%s,\'%s\',item.%s,\'%s\',%s)" class="styled-checkbox" ng-model="item.%s" id="item_%s" />
                <label style="margin:0;    display: block;" for="item_%s" class="styled-checkbox-label">
                        <i class="" ></i>
                </label></td>',
                // $this->getAlias(true),
                $this->getAlias(true),
                $this->property,
                $this->identity,
                str_replace('\\','_',$this->entity),
                (int)$this->confirm,
                $this->getAlias(true),               
                $this->getAlias(true) . '_{{item.' . $this->identity .'}}',               
                $this->getAlias(true) . '_{{item.' . $this->identity .'}}'          
        );
	}
}