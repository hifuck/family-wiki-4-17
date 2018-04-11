<?php
/**
 * 用户行为类
 * @author: jiangpengfei
 * @date:   2018-04-08
 */

namespace App\Controller;

use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use App\ViewController;
use App\DB\UserDB;
use App\Utils\Util;
use App\Utils\Check;
use Conf\ErrorCode;
use Conf\Constant;

class UserCtl extends ViewController{

    function index() {
        $this->profile();
    }

    function profile() {
        $this->assign('profile_active','active');
        $this->assign('word_active','');
        $this->assign('user',$this->user);
        $this->fetch('UserCtl/index.html');
    }

    function myWord() {
        if (!$this->checkLogin() ) {
            return;
        }

        $params = $this->request()->getRequestParam();

        $api = $params['api'] ?? null;
        $pageIndex = $params['p'] ?? 1;
        $pageSize = $params['s'] ?? 10;
        $userId = $this->user['userId'];

        $wordDB = Util::buildInstance('App\DB\WordDB');
        $paging = $wordDB->getUserWordsPaging($pageIndex, $pageSize, $userId);
        $total = $wordDB->getUserWordsCount($userId);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;
        $data['content'] = $paging;
        $data['total'] = $total;

        if ($api == null) {
            $this->assign('profile_active','');
            $this->assign('word_active','active');
            $this->assignMap($data);
            $this->fetch("UserCtl/myword.html");
        } else {
            Util::printResult($this->response,ErrorCode::ERROR_SUCCESS,$data);
        }
    }

    function login() {

    }

    function logout() {
        if ($this->user != null) {
            $userDB = Util::buildInstance('App\DB\UserDB');
            $userDB->deleteToken($this->user['userId'],$this->user['token']);
            $this->response()->setCookie('user',"",time() - 1,'/');
        }
        // 从数据库中使token无效
        $this->fetch('UserCtl/logout.html');
    }

    function checkToken() {
        $params = $this->request()->getRequestParam();

        $systemUrl = Constant::SYSTEM_URL;
        $token = $params['token'] ?? null;

        if ($systemUrl === null || $token === null) {
            Util::printResult($this->response(),ErrorCode::ERROR_PARAM_MISSING,"参数缺失");
            return;
        }

        $userDB = Util::buildInstance('\App\DB\UserDB');
        $user = $userDB->checkToken($token,$systemUrl);


        if (!$user) {
            // 令牌无效
            $this->assign('error',true);
            $this->fetch('UserCtl/checkToken.html');
            return;
        } else {
            // 设置cookie
            $this->assign('nickname',$user->nickname);
            $this->response()->setCookie('user',json_encode($user),time()+3600*24*30,'/');
            $this->fetch('UserCtl/checkToken.html');
            return;
        }
    }

    function onRequest($actionName)
    {
        parent::onRequest($actionName);
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {

    }
}