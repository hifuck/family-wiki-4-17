<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-4
 * Time: 上午11:13
 */

namespace App\Controller\Admin;


class Index extends  Base
{

    function index()
    {
        $this->fetch('Admin/Index/index.html');
        // TODO: Implement index() method.
    }

    function onRequest($actionName)
    {
        parent::onRequest($actionName); // TODO: Change the autogenerated stub
        $arr = ['index'];
        if (in_array($actionName,$arr)){
            $this->checkLoginStatus();
        }
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }
}