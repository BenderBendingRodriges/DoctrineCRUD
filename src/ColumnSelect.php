<?php

namespace DoctrineCRUD;
class ColumnSelect extends ColumnInput{
	public function render(){
		$column = $this;
		return sprintf('<td class="td-with-input">
            <i class="fa fa-pencil fa-2x"></i><select  ng-change="change(item.%s,\'%s\',item.%s,\'%s\',%s)" 
            class="form-control angular-table-select" 
            ng-model="item.%s"
            >%s</select></td>',
                $this->getAlias(true),
                $this->property,
                $this->identity,
                str_replace('\\','_',$this->entity),
                (int)$this->confirm,
                $this->getAlias(true),
                
                implode('',array_map(function($option) use($column){
                    return 
                    sprintf('<option  ng-value="%s" ng-selected="%s">%s</option>',
                        $option['value'],
                        ($option['value'] ?  'item.' . $column->getAlias(true) . ' == '.$option['value'] : '!item.' . $column->getAlias(true)),
                        // $option['value'],
                        $option['label']); },$column->valueOptions))
        );
		// item.%s == %s
	}
	protected $valueOptions;
	
}