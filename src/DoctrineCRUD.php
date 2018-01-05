<?php

namespace DoctrineCRUD;

use Zend\View\Model\ViewModel as ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class DoctrineCRUD {
    
    protected $sm;
    protected $qb;
    protected $columns;
    protected $hiddenColumns;
    public $searchColumns = [];
    protected $selects;
    public $paginator;
    protected $fromAlias;
    protected $buttons;
    protected $footButtons;
    protected $params;
    public $label;
    public $icon;
    protected $secondarySort;
    public function setSecondarySort($val){
        $this->secondarySort = $val;
    }
    
    
    public $partials = array();
    
    public function __construct($qb = null ,$sm = null) {
        if($qb)$this->setQb($qb);
        $this->sm = $sm;
    }
    public function getQb(){
        return $this->qb;
    }
    public function getServiceManager(){return $this->sm;}
    public function setColumns($columns){
        foreach($columns as $col)$this->addColumn($col);
            //$this->columns[] = new Column($col,$this);
        // }
    }
    public function addColumn($column){      
        $col = new Column($column,$this);  
        $this->columns[] = $col;
        if($col->search)$this->searchColumns[] = $col;
    }
    public function addHiddenColumn($column){        
        $col = new HiddenColumn($column,$this);
        $this->hiddenColumns[] = $col;
        if($col->search)$this->searchColumns[] = $col;
            
    }
    public function addColumnInput($type,$column,$options){  
        if($col = ColumnInput::create($type,$column,$options,$this)){
            $this->columns[] = $col;
            if($col->search)$this->searchColumns[] = $col;
        }
    }
    public function setQb($qb){
        $this->qb = $qb;
        $from = $this->qb->getDQLPart('from');
        $this->fromAlias = array_shift($from)->getalias();
    }
    public function setButtons($buttons){
        $this->buttons = array();
        foreach($buttons as $btn)$this->addButton($btn);
    }

    protected static function findActivePage($container,$rm){
        foreach ($container->getPages() as $key => $page) {
            $page = clone $page;
            $page->setRouteMatch($rm);
            $page->setActive(false);


            if($page->isActive())
            {
                
                return $page;
            }
            else if($page->hasPages())
            {
                $found = self::findActivePage($page,$rm);
                if($found)
                    return $found;   
            }
            
        }
        return null;
    }

    protected function checkIsAllowedFromUrl($url){
        $nav = clone $this->sm->get('Zend\Navigation\Navigation');
        $rm = $this->sm->get('Router');
        $req = new \Zend\Http\PhpEnvironment\Request();
        $req->setRequestUri($url);
        $req->setUri($url);
        
        $page = self::findActivePage($nav,$rm->match($req));    
        if(!$page)return false;

        $acl = $this->sm->get('ViewManager')->getViewModel()->getVariable('acl');

        return $acl->isAllowed(null,$page->getResource(),$page->getPrivilege());
    }
    public function addButton($button){
        

        
        
        if($this->checkIsAllowedFromUrl($button['url']))
            $this->buttons[] = new Button($button,$this);
    }
    public function addDelButton($entity = null,$identity = null){
        $user = $this->sm->get('Zend\Authentication\AuthenticationService')->getIdentity();
        if($user->role != 'admin')return;
        // var_dump(get_class($user));

        // die();
        if(!$entity || !$identity){
            $from = $this->qb->getDQLPart('from');   
            $from = $from[0];         
        }
        if(!$entity)$entity = $from->getFrom();
        if(!$identity)$identity = '__'.$from->getalias().'.id__';
        $this->buttons[] = new Button(array(
            'btn'=>'danger',
            'fa'=>'trash',
            'title'=>'Usuń',
            'class' => array('confirm'),
            'url'=> '/table_delete/'.(str_replace('%5C','_',  urlencode($entity))) .'/'.$identity
        ),$this);
    }
    public function setFootButtons($buttons){
        $this->footButtons = array();
        foreach($buttons as $btn)$this->addFootButton($btn);
    }
    public function addFootButton($button){
        if(@$button['dropdown'])
        {
            foreach($button['dropdown'] as $key => $dropbtn){
                if(!$this->checkIsAllowedFromUrl($dropbtn['url'])){
                    unset($button['dropdown'][$key]);
                }
            }
            if(count($button['dropdown']))
            $this->footButtons[] = new FootButton($button,$this);
        }
        else if($this->checkIsAllowedFromUrl($button['url'])){
            $this->footButtons[] = new FootButton($button,$this);
        }
    }
    public function getParams($name = null){
        return $name ? ( is_array($this->params) && isset($this->params[$name]) ? $this->params[$name] : null ) : $this->params;
    }


    
    
    public function hasSearch(){

        return count(array_filter($this->columns, function($c){return $c->search;})) > 0;
        return count($this->searchColumns) > 0;
    }

    public function getSearchLabel(){
        // $searchColumns = array_filter($this->columns, function($c){return $c->search;});
        $searchColumns =$this->searchColumns;
        $searchColumns = array_map(function($c){
            $label = $c->search === TRUE ? $c->label : $c->search;
            return mb_convert_case($label,MB_CASE_LOWER,'UTF-8');
        },$searchColumns);        
        $last = array_pop($searchColumns);
        $label = null;
        $label .= implode(", ",$searchColumns); 
        $label .= ($label) ? " lub " : "";
        $label .= $last;
        $label = "Szukaj po : " . $label;
        return $label;        
    }

    public function prepare($params){

        if($this->qb->getDQLPart('select')){
            $this->selects = array();
            foreach($this->qb->getDQLPart('select') as $p){
                $this->selects = array_merge($this->selects,$p->getParts());   
            }
            
        }
        unset($params['__NAMESPACE__']);
        unset($params['module']);
        $this->params = $params;        
        foreach ($this->columns as $column){
            $column->addSelect($this->selects);
        }
        foreach ($this->hiddenColumns as $column){

            $column->addSelect($this->selects);
        }
        if(count($this->partials)){
            if(!isset($this->partials[$this->fromAlias])){
                $this->selects[] = 'partial '.$this->fromAlias.'.{id}';
                // $this->selects[] = $this->fromAlias;
            }
            foreach($this->partials as $k => $p){
                if(!in_array('id',$p))$p[] = 'id';
                    
                $this->selects[] = $k;//sprintf('partial %s.{%s}',$k,implode(',',$p));
            }
            
        }
       // die(var_dump($this->selects));
        if($this->buttons){
            foreach ($this->buttons as $btn){
                foreach ($btn->selects as $select){
                    list($field,$alias) = $select;

                    $this->selects[$alias] = $field . ' as ' . $alias;
                }
            }
        }
        
        
        $this->qb->select($this->selects);
        
        $order = strtolower($params['direction']);
        $order = in_array($order,array('asc','desc')) ? $order : 'desc';
        $sort = isset($params['sort']) ? $params['sort'] : null;

        // var_Dump($sort);
        if($sort)$this->qb->addOrderBy($sort, $order);
        if($this->secondarySort){
            $this->qb->addOrderBy($this->secondarySort[0], $this->secondarySort[1]);
            // die($this->qb->getQuery()->getDQL());
        }

        
        if(isset($this->params['search']) && ($fraze = $this->params['search'])){
            $formatArray = array();
            $searchColumns = array_filter($this->columns, function($c){return $c->search;});
            
            foreach($searchColumns as $column){
                list($field,$alias) = $column->field;
                $formatArray[] = $field . ' LIKE \'%1$s\' ';
            }
            
            
            $format = implode(" OR ",$formatArray);
            $frazes = explode(" ",$fraze);
            foreach($frazes as $search){
                $part = new \Doctrine\ORM\Query\Expr\Orx();

                $part->add(sprintf($format,'%'.$search.'%'));
                $searchJ = str_replace('"', "", json_encode(lcfirst($search)));
                $searchJ = str_replace('\\', "\\\\", $searchJ);
                $part->add(sprintf($format,'%'.$searchJ.'%'));
                $searchJ = str_replace('"', "", json_encode(ucfirst($search)));
                $searchJ = str_replace('\\', "\\\\", $searchJ);
                $part->add(sprintf($format,'%'.$searchJ.'%'));
                $this->qb->andWhere($part);
            }
            // die($this->qb->getQuery()->getDQL());
        }

        if(isset($this->params['filter'])){
            $criteria = array();
            foreach($this->params['filter'] as $key => $val){
                if(is_object($val))$val = (array)$val;

                foreach($val as $subkey => $subval){
                    if(is_object($subval))$subval = (array)$subval;
                    if(is_array($subval)){
                        $criteria[$key.'.'.$subkey] = (array)$subval;    
                    }else{
                        $criteria[$key] = array($subkey => $subval); 
                        
                    }
                    
                }
            }
            // var_dump($criteria);
            
            foreach($criteria as $prop => $crit){
                $crit = array_filter($crit);
                // var_dump($crit[key($crit)]);
                // var_dump($key = key($crit));
                // die();
                switch($key = key($crit)){
                    case 'eq':


                        if($crit[$key])
                        $this->qb->andWhere($prop.' = '.$crit[$key]);
                        break;
                     case 'like':                        
                        if($crit[$key]){
                            $propN = str_replace(".","_",$prop);
                            $this->qb->andWhere($prop." LIKE :filter$propN")->setParameter('filter'.$propN,$crit[$key]);
                        }
                        
                        break;
                    case 'address':
                        if($crit[$key])
                        $this->qb->andWhere($prop." LIKE '%;".$crit[$key].";%'");
                        // die($prop." LIKE '%;".$crit[$key].";%'");
                        break;
                     case 'discr':
                        $prop = str_replace(".discr","",$prop);
                        $part = new \Doctrine\ORM\Query\Expr\Orx();
                        foreach($crit[$key] as $class_name => $is){

                            if(!$is)continue;
                            $class_name = str_replace("__","\\",$class_name);
                            // var_dump($class_name);
                            $part->add($prop.' INSTANCE OF '.$class_name);
                            
                        }
                        if($part->count())
                            $this->qb->andWhere($part);
                        // var_dump($prop);
                        // die($this->qb->getDQL());
                        break;
                    case 'in':

                        $in = (array)$crit[$key];
                        $in = array_filter($in,function($r){return $r;});
                        
                        $in = array_map(function($r){
                            return $r;
                        },$in);
                        if(!empty($in)){
                            $propN = str_replace(".","_",$prop);
                            $this->qb->andWhere($prop." IN (:filter$propN)")->setParameter('filter'.$propN,$in);
                        }
                        
                        break;
                    case 'kin':

                        $in = (array)$crit[$key];
                        $in = array_filter($in,function($r){return $r;});
                        $in = array_keys($in);
                        $in = array_map(function($r){
                            // if(is_numeric($r)) return (int)$r;
                            return $r;
                        },$in);
                        // var_Dump($in);//die();
                        if(!empty($in)){
                            // var_dump($in);
                            // var_dump($key);
                            // var_dump($prop);
                            $propN = str_replace(".","_",$prop);
                            $this->qb->andWhere($prop." IN (:filter$propN)")->setParameter('filter'.$propN,$in);
                        }
                        
                        break;
                    case 'range':
                        // $range = json_decode($crit[$key]);
                        // if($range){

                        // }
                        $range = explode(";",$crit[$key]);
                        $this->qb->andWhere($prop.' BETWEEN '.$range[0] . ' AND ' . $range[1]);
                        break;
                    case 'bool':
                        $value = $crit[$key];
                        if((int)$value == 1)
                            $this->qb->andWhere($prop.' = true ');
                        elseif((int)$value == 0)
                            $this->qb->andWhere($prop.' = false ');
                        break;
                    case 'rangeTime':
                        $range = json_decode($crit[$key]);
                        if(!$range){
                            $r = explode(" - ",$crit[$key]);
                            $range = (object)[
                                'from' => $r[0] . " 00:00:00",
                                'to' => $r[1] . " 23:59:59",
                            ];
                        }
                        // die(var_dump($range));
                        // $this->qb->andWhere($prop.' BETWEEN '.$range->from . ' AND ' . $range->to);
                        $this->qb->andWhere($prop." >= '".$range->from."'");
                        $this->qb->andWhere($prop." <= '".$range->to."'");
                        break;
                        
                }
            }
        }
        // die();
        // die($this->qb->getQuery()->getSQL());
        
        $ormP = new ORMPaginator($this->qb);

       
        // die(var_dump($this->qb->getDQLParts('where')));
        $ormP->setUseOutputWalkers(false);
        $adapter = new DoctrineAdapter($ormP);
        $this->paginator = new Paginator($adapter);
        $this->paginator->setDefaultItemCountPerPage(20);
        
        if(isset($params['itemCount']))
            $this->paginator->setDefaultItemCountPerPage($params['itemCount']);
        $page = (int)$params['page'];
        if($page) $this->paginator->setCurrentPageNumber($page);
        return $this;
    }

    public function setServiceMamanger($sm){
            $this->sm = $sm;
    }

    public function getResponse($sm,$template = 'table'){
        
        $this->sm = $sm;
        $reqest = $sm->get('request');
        if($reqest->isPost()){
            $data = $reqest->getPost();
            if(isset($data['search'])){
                $routeMatch = $sm->get('router')->match($reqest);
                $params = $routeMatch->getParams();
                unset($params['__NAMESPACE__']);
                unset($params['module']);
//                die(var_dump($params));
                $headers = new \Zend\Http\Headers();
                $url = $sm->get('viewhelpermanager')->get('url');
//                die(var_dump($routeMatch));
                $data['search'] = mb_convert_case($data['search'],MB_CASE_LOWER,'UTF-8');
                $routeName = $routeMatch->getMatchedRouteName();
//                $routeName = strpos($routeName,'/wild') !== FALSE ? $routeName : $routeName .'/wild';
                $headers->addHeaderLine('Location: ' . $url($routeName,array_merge($params,array('search'=>$data['search'])),true));
                $response = new \Zend\Http\PhpEnvironment\Response();
                $response->setStatusCode(302);                
                $response->setHeaders($headers);
                return $response;
            }
        }
        // die($template);
        $view = new ViewModel();
        $view->setTemplate($template);
        $view->setVariable('table', $this);
        return $view;
    }
    public function getIcon(){
        if($this->icon === FALSE)return null;
        $icon = $this->icon ? $this->icon : 'fa-list';
        return sprintf('<i class="fa fa-fw fa-lg %s"></i> ',$icon);
    }

    public function getLabel(){
         
        $label = null;
        if($this->label){
            $label = $this->label;
        }elseif($active = $this->sm->get('navigation')->findOneByActive(true)){


            $label = $active->getLabel();
        }
        return $this->getIcon() . $label;
    }
    
    public function __call($name, $arguments) {
        if(strpos($name,'get') === 0 && property_exists($this, $propName = lcfirst(substr($name, 3)))){
            return $this->$propName;
        }
        die($name);
    }
}

?>
