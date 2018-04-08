<?php
/**
 * 用户资料类
 * @author: jiangpengfei
 * @date:   2018-04-08
 */

namespace App\Controller;

use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use App\ViewController;
use App\DB\WordDB;
use App\Utils\Util;
use App\Utils\Check;
use App\Model\Word;
use Conf\ErrorCode;

class UserProfile extends ViewController{

    function index() {
        $this->fetch('UserCenter/UserProfile/index.html');
    }

    function onRequest($actionName)
    {
        $this->response()->withHeader('Access-Control-Allow-Origin','*');
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {

    }
}