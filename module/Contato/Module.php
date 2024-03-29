<?php
/**
 * namespace para nosso modulo contatos
 */
namespace Contato;

use Contato\Model\Contato,
    Contato\Model\ContatoTable;

use Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway;
 
class Module
{
    /**
     * include de arquivo para outras configuracoes desse modulo
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
 
    /**
     * autoloader para nosso modulo
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * Register View Helper
     */
    public function getViewHelperConfig()
    {
        return array(
            # registrar View Helper com injecao de dependecia
            'factories' => array(
                'menuAtivo'  => function($sm) {
                    return new View\Helper\MenuAtivo($sm->getServiceLocator()->get('Request'));
                },
                'message' => function($sm) {
                    return new View\Helper\Message($sm->getServiceLocator()->get('ControllerPluginManager')->get('flashmessenger'));
                },
            ),
            'invokables' => array(
                'filter' => 'Contato\View\Helper\ContatoFilter'
            )
        );
    }

    /**
     * Register Services
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ContatoTableGateway' => function ($sm) {
                    // obter adapter db atraves do service manager
                    // $adapter = $sm->get('AdapterDb');
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');

                    // configurar ResultSet com nosso model Contato
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Contato());

                    // return TableGateway configurado para nosso model Contato
                    return new TableGateway('contatos', $adapter, null, $resultSetPrototype);
                },            
                'ModelContato' => function ($sm) {
                    // return instacia Model ContatoTable
                    return new ContatoTable($sm->get('ContatoTableGateway'));
                }
            )
        );
    }


    /**
     * Register Controller Plugin
     */
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'cache' => function($sm) {
                    return new Controller\Plugin\Cache($sm->getServiceLocator()->get('Cache\FileSystem'));
                },
            ),
        );
    }
}