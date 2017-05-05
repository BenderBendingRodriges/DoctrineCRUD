<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DoctrineCRUD;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;



class Module
{
    public function getConfig(){
        return array(
            'router' => array(                
                'routes' => array(
                    'table_delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/table_delete/:entity/:identity',
                            'defaults' => array(
                                'module' => 'table',
                                'controller' => Controller\IndexController::class,
                                'action'     => 'delete',
                            ),
                        ),
                    ),
                    'table_update' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/table_update/:entity/:identity',
                            'defaults' => array(
                                'module' => 'table',
                                'controller' => Controller\IndexController::class,
                                'action'     => 'update',
                            ),
                        ),
                    ),
                ),
            ),
            'controllers' => array(
                // 'invokables' => array(
                //     'DoctrineCRUD\Controller\Index' => 'DoctrineCRUD\Controller\IndexController',
                // ),
            ),
            'view_manager' => array(
                'template_path_stack' => array(
                    'DoctrineCRUD_view' => __DIR__ . '/view',
                    'DoctrineCRUD_partial' => __DIR__ . '/view/partial',
                ),
            ),
            'view_helpers' => array(
                'factories' => array(
                    'DoctrineCRUD' => function($sm){return new \DoctrineCRUD\Helper\Table($sm);},
                    'BodyColumn' => function($sm){return new \DoctrineCRUD\Helper\BodyColumn($sm);},
                    'HeaderColumn' => function($sm){return new \DoctrineCRUD\Helper\HeaderColumn($sm);},
                    'BodyButton' => function($sm){return new \DoctrineCRUD\Helper\BodyButton($sm);},
                    'footButton' => function($sm){return new \DoctrineCRUD\Helper\FootButton($sm);},
                    'TableToggleList' => function($sm){return new \DoctrineCRUD\Helper\TableToggleList($sm);}                    
                ),
            )
        );
    }
    
    public function getAutoloaderConfig(){
        
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ .'/src/',
                ),
            ),
        );
    }
}
