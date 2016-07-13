<?php
/**
 * Log writer object definition
 *
 * @category App
 * @package App_DI
 * @copyright company
 */
class App_DI_Definition_GeneralLog
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $path = ROOT_PATH . '/data/logs/general.log';
        $logger = new Zend_Log();
        $logger->addWriter(new Zend_Log_Writer_Stream($path));
        
        if (!Zend_Registry::get('IS_PRODUCTION')) {
            $logger->addWriter(new Zend_Log_Writer_Firebug());
        }
        
        return $logger;
    }
}
