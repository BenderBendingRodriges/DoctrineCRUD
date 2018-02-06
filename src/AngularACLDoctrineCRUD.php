<?php

namespace DoctrineCRUD;

use Zend\View\Model\ViewModel as ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;


class AngularACLDoctrineCRUD  extends AngularDoctrineCRUD{

    protected $aclButtons = [];

    public function addACLButton($button){
        $url = $button['url'];

        $nav = clone $this->sm->get('Zend\Navigation\Navigation');
        $nav2 = clone $this->sm->get('custom');
        $rm = $this->sm->get('Router');
        $req = new \Zend\Http\PhpEnvironment\Request();
        $req->setRequestUri($url);
        $req->setUri($url);
        
        $page = self::findActivePage($nav,$rm->match($req));  
        $page = $page ? $page : self::findActivePage($nav2,$rm->match($req));  
        $acl = $this->sm->get('ViewManager')->getViewModel()->getVariable('acl'); 

        $this->aclButtons[] = new AclButton($button,$this,$acl,$acl->getResource($page->getResource()),$page->getPrivilege());
        $this->buttons[] = new AclButton($button,$this,$acl,$acl->getResource($page->getResource()),$page->getPrivilege());
    }
    public function addDelButton($entity = null,$identity = null){
        $nav = $this->sm->get('Zend\Navigation\Navigation');
        $activeElement = $nav->findOneBy('active', 1);       
        $resource =  $activeElement ? $activeElement->getResource() : null;
        $acl = $this->sm->get('ViewManager')->getViewModel()->getVariable('acl');

        // if(!$acl->isAllowed(null,$resource,'delete'))return;
        // // if(return $acl->isAllowed(null,$page->getResource(),$page->getPrivilege());)
        
        if(!$entity || !$identity){
            $from = $this->qb->getDQLPart('from');   
            $from = $from[0];         
        }
        if(!$entity)$entity = $from->getFrom();
        if(!$identity)$identity = '__'.$from->getalias().'.id__';


        // die('/table_delete/'.(str_replace('%5C','_',  urlencode($entity))) .'/'.$identity);
        $btn = new AclButton(array(
            'btn'=>'danger',
            'fa'=>'trash',
            'title'=>'Usuń',
            'class' => array('confirm'),
            'url'=> '/table_delete/'.(str_replace('%5C','_',  urlencode($entity))) .'/'.$identity
        ),$this,$acl,$resource,'delete');
        $this->aclButtons[] = $btn;
        $this->buttons[] = $btn;
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
            $items = array();
            foreach($this->paginator->getCurrentItems() as $item){
                $row = $item;
                if(isset($row[0]))unset($row[0]);
                foreach($this->getColumns() as $column){
                    list($field,$alias) = $column->getField();
                    $row[$alias] = $column->getValue($item);
                }
                $row['buttons'] = [];
                foreach($this->aclButtons as $aclButton)
                {
                    
                    $row['buttons'][] = $aclButton->toJSON($item);//['adsdsa'];    
                }
                
                $items[] = $row;
            }
            $result = new JsonModel(array(
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
            $view->setTemplate('angular-acl-table');    
            define('tableOn',true);
            $view->setVariable('table', $this);
            return $view;
        }
        
    }
    
}

?>
