<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-4
 * Time: 上午11:13
 */

namespace App\Controller\Admin;


use App\ViewController;

class Index extends  ViewController
{

    function index()
    {
        $this->fetch('Admin/Index/index.html');
        // TODO: Implement index() method.
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        $this->response()->withHeader('Access-Control-Allow-Origin', '*');
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