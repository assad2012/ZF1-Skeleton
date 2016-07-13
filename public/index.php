<?php
/**
* @file index.php
* @synopsis  入口文件
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2016-07-13 13:14:31
*/

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

$paths = array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
);

set_include_path(implode(PATH_SEPARATOR, $paths));

include('Base/functions.php');

include('Tracy/tracy.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT);
Debugger::$showBar = False;

require(APPLICATION_PATH . '/Bootstrap.php');
$bootstrap = new Bootstrap();
$bootstrap->runApp();

