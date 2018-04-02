<?php
/**
 * 词条的控制器类
 * author: jiangpengfei
 * Date: 2018/4/2
 */

namespace App\Controller;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;

use App\DB\WordDB;

class Word extends AbstractController
{

    function index()
    {
        $this->actionNotFound();
    }

    /**
     * 添加词条
     */
    function addWord() {

    }

    /**
     * 编辑词条
     */
    function editWord() {

    }

    /**
     * 删除词条
     */
    function deleteWord() {

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