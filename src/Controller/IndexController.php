<?php

namespace DoctrineCRUD\Controller;

use Zend\View\Model\ViewModel as ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class IndexController extends \Zend\Mvc\Controller\AbstractRestfulController{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;

    /**
     * ServiceLocator.
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    public function __construct(\Doctrine\ORM\EntityManager $em){
        $this->em = $em;
    }

   

    public function deleteAction(){
        $entity = $this->params('entity');
        $entity = str_replace("_","\\",$entity);


        $obj = $this->em->find($entity,$this->params('identity'));
        if($obj){
            $this->em->remove($obj);
            $this->em->flush();
        }

        if($this->getRequest()->getMethod() == \Zend\Http\Request::METHOD_DELETE){
            // die(json_encode(['ok' => true]));
            return new \Zend\View\Model\JsonModel(['success' => true]);
        }
        
        if(isset($_SERVER['HTTP_REFERER']))
            return $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
        else
            return $this->redirect()->toUrl("/");
    }

    public function updateAction(){
       	// $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $this->params('entity');
        $entity = str_replace("_","\\",$entity);
        // die(var_dump($this->params('identity')));
        $meta = $this->em->getClassMetadata($entity);
        $entity =  $this->em->find($entity,$this->params('identity'));
        $data = json_decode(file_get_contents("php://input"));


        
        
        foreach($data as $param => $val){
            if(isset($meta->associationMappings[$param])){
                // var_Dump($meta->associationMappings[$param]);die();
                $val = $this->em->find($meta->associationMappings[$param]['targetEntity'],$val);
            }
        	$entity->{$param} = $val;
        }
        $this->em->persist($entity);
        $this->em->flush();
        die(var_dump($entity->{$param}));
        // $this->redirect()->toUrl($_SERVER['HTTP_REFERER']);
        // 
        $entity = $this->params('entity');
        $entity = str_replace("_","\\",$entity);
        die(var_dump($entity));
    }
}

?>
