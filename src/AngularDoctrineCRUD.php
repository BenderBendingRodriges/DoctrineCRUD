<?php

namespace DoctrineCRUD;

use Zend\View\Model\ViewModel as ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
class AngularDoctrineCRUD extends DoctrineCRUD{
    protected $filterForm;
    protected $cookieKey;
    protected $initalState;
    protected $filterMap = [];

    protected $querySuffix = null;
    public function getQuerySuffix(){ return $this->querySuffix ? '?query_suffix=' . $this->querySuffix : '';}
    public function setQuerySuffix($value){ return $this->querySuffix = $value;}

    public function getInitalState(){return $this->initalState;}
    public function hasInitalState(){return $this->initalState != null;}
    public function setInitalState($val){return $this->initalState = $val;}


    public function getCookieKey(){return $this->cookieKey;}
    public function hasCookieKey(){return $this->cookieKey != null;}
    public function ngCookieKey(){return $this->hasCookieKey() ? ' cookiekey="\''.$this->cookieKey.'\'" ' : null;}
    public function setCookieKey($value){$this->cookieKey = $value;}

    public function getFilterForm(){
        return $this->filterForm;
    }
    public function hasFilterForm(){
        return $this->filterForm != null;
    }
    public function getFilterMap(){
        if(!$this->filterForm)
            return null;
        foreach($this->filterForm->getElements() as $k => $element){
            if(method_exists($element, 'getValueOptions')){
                foreach($element->getValueOptions() as $valueOption){
                    $key = str_replace('query.filter.','',$valueOption["attributes"]['data-ng-model']);
                    $key = str_replace(['[',']'],['.',''],$key);

                    if(count(explode('.',$key )) == 3){
                        // var_dump($valueOption);
                        // foreach()
                        $this->filterMap[$key][$valueOption['value']] = array($element->getLabel(),$valueOption['label']);
                    }
                    else
                        $this->filterMap[$key] = array($element->getLabel(),$valueOption['label']);
                }
            }else{

                $key = str_replace('query.filter.','',$element->getAttribute("data-ng-model"));
                $this->filterMap[$key] = array($element->getLabel(),'__value__');
            }
        }
        // var_dump($this->filterMap);
        // die();
        return $this->filterMap;
    }
    public function addFilter($property,$label,$type,$expr,$attr = array(),$options= array()){
        // 'category', 'Kategoria','\Stmx\Form\Element\TreeSelect',array(),array(
        if(!$this->filterForm){
            $this->filterForm = new \Zend\Form\Form();
            $this->filterForm->setOption('columns',1);
        }
        $element = new $type(str_replace(".","_",$property));
        $element->setLabel($label);
        $attr = array_merge(array(
            'class'=>'form-control',
            'data-ng-change' => "doFilter()",
            'data-ng-model' => 'query.filter.'.$property.'.'.$expr
        ),$attr);


        // $this->filterMap[]
        $element->setAttributes($attr);
        $element->setOptions($options);
        $this->filterForm->add($element);


        
        
        $this->filterForm->setOption('columns',max($this->filterForm->getOption('columns'),$element->getOption('col')));
    }
    
    public function getResponse($sm){
        $qbc = clone $this->qb;
        $qbc->select('COUNT (DISTINCT '.$qbc->getRootAlias().')');
        $qbc->resetDQLPart('groupBy');
        $initialCount = (int)$qbc->getQuery()->getSingleScalarResult();
        
        $this->sm = $sm;
        $request = $sm->get('request');
        if($request->isXmlHttpRequest() && $request->isPost() || $_GET['debug']){            
            $data = json_decode(file_get_contents("php://input"));
            $this->prepare((array)$data);

            // die(var_dump(get_class_methods($this->paginator)));
            // $table->paginator->getQb()->orWhere('d.id = p.id');
            $items = array();
            foreach($this->paginator->getCurrentItems() as $item){
                $row = $item;
                if(isset($row[0]))unset($row[0]);
                foreach($this->getColumns() as $column){
                    list($field,$alias) = $column->getField();
                    $row[$alias] = $column->getValue($item);
                }
                $items[] = $row;
            }
            $result = new JsonModel(array(
                // 'query' => $this->qb->getQuery()->getSQL(),
                'items' => $items,
                'defaultItemCountPerPage' => $this->paginator->getDefaultItemCountPerPage(),
                'success'=>true,
                'total' => $this->paginator->getTotalItemCount(),
                'totalUnfilter' => $initialCount,
                'pages' => $this->paginator->getPages()->pageCount,
                'page' => $this->paginator->getCurrentPageNumber(),
            ));
            return $result;
        }else{
            $view = new ViewModel();
            if(isset($_GET['control-side']) ){
                    $view->setTemplate('control-side');
                    $view->setTerminal(true);
            }else{
                    $view->setTemplate('angular-table');    
            }
            define('tableOn',true);
            $view->setVariable('table', $this);
            return $view;
        }
        
    }
    
}

?>
