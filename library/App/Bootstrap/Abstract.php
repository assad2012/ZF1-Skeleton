<?php
/**
* @file Abstract.php
* @synopsis  App_Bootstrap_Abstract
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2016-07-13 12:11:04
*/

abstract class App_Bootstrap_Abstract
{
    private $_bootstrapped = array();
    protected $_first = array();
    protected $_last = array();
    
    public function __construct($boostrapEverything = TRUE)
    {
        if ($boostrapEverything)
        {
            $this->boostrapEverything();
        }
    }
    
    public function boostrapEverything()
    {
        foreach ($this->_first as $resource)
        {
            $this->bootstrap($resource);
        }
        
        $methods = get_class_methods($this);
        foreach ($methods as $method)
        {
            if (strpos($method, '_init') === 0)
            {
                $resource = substr($method, 5);
                if (!in_array($resource, $this->_first) && !in_array($resource, $this->_last))
                {
                    $this->bootstrap($resource);
                }
            }
        }
        
        foreach ($this->_last as $resource)
        {
            $this->bootstrap($resource);
        }
    }
    
    public function bootstrap($resources)
    {
        if (!is_array($resources))
        {
            $resources = array($resources);
        }

        foreach ($resources as $resource)
        {
            if (!in_array($resource, $this->_bootstrapped))
            {
                $method = '_init' . $resource;
                if (method_exists($this, $method))
                {
                    call_user_func(array($this, $method));
                    $this->_bootstrapped[] = $resource;
                }else
                {
                    throw new Zend_Exception('Method ' . $method . ' could not be found in order to boostrap ' . $resource);
                }
            }
        }
    }
}
