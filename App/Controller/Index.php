<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 11:51
 */

namespace App\Controller;


use App\Task;
use Core\AbstractInterface\AbstractController;
use Core\Component\Barrier;
use Core\Component\Logger;
use Core\Http\Message\Status;
use Core\Swoole\AsyncTaskManager;
use Core\Swoole\Server;
use Core\UrlParser;
use App\ViewController;

use Conf\ErrorCode;
use App\Utils\Util;

class Index extends ViewController
{
    function index()
    {
        $params = $this->request()->getRequestParam();

        $api = $params['api'] ?? null;

        if ($api !== null) {
            // 返回接口数据
            $data['test'] = 'test data';
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            // 渲染页面直接输出
            $this->assign('user',"xinhuo");
            $this->fetch('Index/index.html');
        }
    }

    function onRequest($actionName)
    {
        $this->response()->withHeader('Access-Control-Allow-Origin','*');
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFount() method.
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {
        // TODO: Implement afterResponse() method.
    }

    function shutdown(){
        Server::getInstance()->getServer()->shutdown();
    }
    function router(){
        $this->response()->write("your router not end");
    }

}