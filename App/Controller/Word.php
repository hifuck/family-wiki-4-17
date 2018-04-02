<?php
/**
 * 词条的控制器类
 * author: jiangpengfei
 * Date: 2018/4/2
 */

namespace App\Controller;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use App\ViewController;
use App\DB\WordDB;
use App\Utils\Util;

class Word extends ViewController
{

    function index()
    {

    }

    /**
     * 添加词条
     */
    function addWordVerify() {

    }

    /**
     * 编辑词条
     */
    function editWordVerify() {

    }

    /**
     * 删除词条
     */
    function deleteWordVerify() {

    }

    function onRequest($actionName)
    {
        $this->response()->withHeader('Access-Control-Allow-Origin','*');

        $params = $this->request()->getRequestParam();
        $idToken = $params['id_token'] ?? null;
        $deviceCode = $params['deviceCode'] ?? '';
        if($idToken == null){
            Util::printResult($this->response(),"-20000001","未登录");
            $this->response()->end();
            return;
        }else{
            $filter = Filter::getInstance();
            $userId = $filter->validateUser($idToken, $deviceCode);
            if(!$userId){
                Util::printResult($this->response(),"-20000001","未登录");
                $this->response()->end();
                return;
            }else{
                $this->userId = $userId;
            }
        }

        $this->wordDB = Util::buildInstance('\App\DB\WordDB');
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {

    }

}