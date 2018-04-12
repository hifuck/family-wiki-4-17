<?php

namespace App;

use Core\Component\Di;
use Core\Component\Sys\SysConst;
use Core\AbstractInterface\AbstractController;
use Core\Http\Request;
use Core\Http\Response;
use App\Utils\Util;
use Conf\Constant;
use Conf\ErrorCode;
/**
 * 视图控制器
 * Class ViewController
 * @author  : evalor <master@evalor.cn>
 * @package App
 */
abstract class ViewController extends AbstractController
{
    protected $view;
    protected $user = null;
    protected $api = null;

    /**
     * 初始化模板引擎
     * ViewController constructor.
     */
    function __construct()
    {
        $this->view = new \Smarty();
        $tempPath   = Di::getInstance()->get(SysConst::DIR_TEMP);    # 临时文件目录
        $this->view->setCompileDir("{$tempPath}/templates_c/");      # 模板编译目录
        $this->view->setCacheDir("{$tempPath}/cache/");              # 模板缓存目录
        $this->view->setTemplateDir('App/Views/');    # 模板文件目录
        $this->view->setCaching(false);
        $this->view->setConfigDir('App/Views/Include/config');
    }

    /**
     * 输出模板到页面
     * @param  string|null $template 模板文件
     * @author : evalor <master@evalor.cn>
     * @throws \Exception
     * @throws \SmartyException
     */
    function fetch($template = null)
    {
        $this->response()->withHeader("Content-type","text/html;charset=utf-8");
        $content = $this->view->fetch($template);
        $this->response()->write($content);
        $this->view->clearAllAssign();
        $this->view->clearAllCache();
    }

    /**
     * 添加模板变量
     * @param array|string $tpl_var 变量名
     * @param mixed        $value   变量值
     * @param boolean      $nocache 不缓存变量
     * @author : evalor <master@evalor.cn>
     */
    function assign($tpl_var, $value = null, $nocache = false)
    {
        $this->view->assign($tpl_var, $value, $nocache);
    }

    /**
     * 将map添加到模板变量中
     */
    function assignMap($map, $nocache = false) {
        $self = $this;
        if (is_array($map)) {
            \array_walk($map, function($value, $key) use($self, $nocache) {
                $this->assign($key, $value, $nocache);
            });
        }
    }

    function onRequest($actionName) {
        $cookie = $this->request()->getSwooleRequest()->cookie;
        $params = $this->request()->getRequestParam();
        $this->api = $params['api'] ?? null;

        if(isset($cookie['user'])) {
            $user = json_decode($cookie['user'],true);
            $this->assign('nickname',$user['nickname']);
            $this->assign('photo',$user['photo']);
            $this->user = $user;
        } else {
            $this->user = null;
        }
    }

    function checkLogin() {
        if ($this->user != null) {
            $userId = $this->user['userId'];
            $token = $this->user['token'];
            // 验证该用户的userId和token是否对应
            $userDB = Util::buildInstance('App\DB\UserDB');
            $this->user = $userDB->validateUser($userId,$token);
        } else {
            // 检查token是否通过post传过来
            $params = $this->request()->getRequestParam();
            $idToken = $params['id_token'] ?? null;
            if ($idToken != null) {
                $arr = explode('|',$idToken);
                if (count($arr) == 3) {
                    $userId = $arr[0];
                    $token = $arr[1];
                    // 验证该用户的userId和token是否对应
                    $userDB = Util::buildInstance('App\DB\UserDB');
                    $this->user = $userDB->validateUser($userId,$token);
                }
            }
        }

        if ($this->user == null) {

            if ($this->api) {
                Util::printResult($this->response(),ErrorCode::ERROR_LOGIN,"当前未登录");
                $this->response()->end();
                return false;
            } else {
                $ssoUrl = Constant::SSO_SYSTEM_URL;
                $systemUrl = Constant::SYSTEM_URL;
                $this->response()->redirect("http://$ssoUrl/#/login/subSystem/$systemUrl%2fUserCtl%2fcheckToken");
                $this->response()->end();
                return false;
            }
        } else {
            $this->assign('nickname',$this->user['nickname']);
            $this->assign('photo',$this->user['photo']);
            return true;
        }
    }
}