<?php

namespace App;

use Core\Component\Di;
use Core\Component\Sys\SysConst;
use Core\AbstractInterface\AbstractController;
use Core\Http\Request;
use Core\Http\Response;

/**
 * 视图控制器
 * Class ViewController
 * @author  : evalor <master@evalor.cn>
 * @package App
 */
abstract class ViewController extends AbstractController
{
    protected $view;

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
}