<?php
/**
 * @file IndexController.php
 * @synopsis  首页
 * @author Yee, <rlk002@gmail.com>
 * @version 1.0
 * @date 2016-07-13 13:27:52
 */

class IndexController extends App_Controller
{
    public $posts;
    public function init()
    {
        parent::init();
        $this->posts = import('posts');
    }

    public function indexAction()
    {
        $post = $this->posts->findById(2);
        $this->smarty->assign("post", $post);
        $this->smarty->assign('hello', 'Hello World, this is a test site');
    }
}
