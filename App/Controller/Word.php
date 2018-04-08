<?php
/**
 * 词条的控制器类
 * author: jiangpengfei
 * Date: 2018/4/2
 */

namespace App\Controller;


use App\Utils\Util;
use App\ViewController;
use Conf\ErrorCode;
use Core\Http\Message\Status;

class Word extends ViewController
{

    function index()
    {
        $this->actionNotFound();
    }

    function viewAdd()
    {
        // 渲染页面直接输出
        $this->assign('user', "xinhuo");
        $this->fetch('Word/add.html');
    }

    /**
     * 添加词条
     */
    function add()
    {
        $params = $this->request()->getRequestParam();

        $api = $params['api'] ?? null;

        if ($api !== null) {
            // 返回接口数据
            $data['test'] = 'test data';
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->actionNotFound('add');
        }
    }

    /**
     * 编辑词条
     */
    function edit()
    {

    }

    /**
     * 删除词条
     */
    function delete()
    {

    }

    function viewAddWordType() {

    }


    function onRequest($actionName)
    {
        $this->response()->withHeader('Access-Control-Allow-Origin', '*');
//
//        $params = $this->request()->getRequestParam();
//        $idToken = $params['id_token'] ?? null;
//        $deviceCode = $params['deviceCode'] ?? '';
//        if ($idToken == null) {
//            Util::printResult($this->response(), "-20000001", "未登录");
//            $this->response()->end();
//            return;
//        } else {
//            $filter = Filter::getInstance();
//            $userId = $filter->validateUser($idToken, $deviceCode);
//            if (!$userId) {
//                Util::printResult($this->response(), "-20000001", "未登录");
//                $this->response()->end();
//                return;
//            } else {
//                $this->userId = $userId;
//            }
//        }
//
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