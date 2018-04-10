<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:22
 */

namespace App\Controller\Admin;


use App\DB\WordTemplateDB;
use App\Utils\Check;
use App\Utils\CheckException;
use App\Utils\Util;
use App\ViewController;
use Conf\ErrorCode;

class WordTemplate extends ViewController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->getWordTemplatePaging();
        $this->fetch('Admin/WordTemplate/index.html');
    }

    function viewAdd()
    {
        $this->fetch('Admin/WordTemplate/add.html');
    }

    function viewEdit()
    {
        $id = $this->request()->getRequestParam('id');

        if ($id) {

            $this->getWordTemplateById();
        }

        $this->fetch('Admin/WordTemplate/edit.html');
    }

    /**
     * 添加模板
     * @return bool
     */
    function add()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $typeId = Check::checkInteger($params['typeId'] ?? null);
            $name = Check::check($params['name'] ?? null);
            $content = Check::check($params['content'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordTemplate/add.html', $api);
            return;
        }

        if ($typeId == null || $name == null || $content == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordTemplate/add.html', $api);
            return;
        }

        $wordDb = new WordTemplateDB();

        $result = $wordDb->addWordTemplate($typeId, $name, $content);

        if ($api !== null) {
            $data['templateId'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('row', $result);
            $this->fetch('Admin/WordTemplate/add.html');
        }
    }

    /**
     * 编辑模板
     * @return bool
     */
    function edit()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $templateId = Check::checkInteger($params['templateId'] ?? null);
            $typeId = Check::checkInteger($params['typeId'] ?? null);
            $name = Check::check($params['name'] ?? null);
            $content = Check::check($params['content'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordTemplate/edit.html', $api);
            return;
        }

        if ($templateId == null || $typeId == null || $name == null || $content == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordTemplate/edit.html', $api);
            return;
        }

        $wordTemplateDb = new WordTemplateDB();

        $result = $wordTemplateDb->editWordTemplateById($templateId, $typeId, $name, $content);

        if ($api !== null) {
            $data['updateRow'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('updateRow', $result);
            $this->fetch('Admin/WordTemplate/edit.html');
        }
    }

    /**
     * 删除模板
     * @return bool
     */
    function del()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $templateId = Check::checkInteger($params['templateId'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordTemplate/del.html', $api);
            return;
        }

        if ($templateId == null) {
            Util::printError($this, ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordTemplate/del.html', $api);
            return;
        }

        $wordTemplateDb = new WordTemplateDB();
        $result = $wordTemplateDb->delWordTemplateById($templateId);

        if ($api !== null) {
            $data['delRow'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('delRow', $result);
            $this->fetch('Admin/WordTemplate/del.html');
        }
    }

    /**
     * 分页获取分类模板
     */
    function getWordTemplatePaging()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $pageIndex = Check::checkInteger($params['pageIndex'] ?? 1);
            $pageSize = Check::checkInteger($params['pageSize'] ?? 10);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordTemplate/index.html', $api);
            return;
        }

        $wordTemplateDb = new WordTemplateDB();
        $result = $wordTemplateDb->getWordTemplatePaging($pageIndex, $pageSize);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;
        $data['content'] = $result;
        $data['total'] = $wordTemplateDb->countWordTemplate();

        if ($api !== null) {
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $data);
            $this->fetch('Admin/WordTemplate/index.html');
        }
    }

    /**
     * 通过模板Id获取模板内容
     */
    function getWordTemplateById()
    {
        $params = $this->request()->getRequestParam();
        $api = $params['api'] ?? null;

        try {
            $id = Check::checkInteger($params['id'] ?? null);
        } catch (CheckException $e) {
            Util::printError($this, $e->getCode(), $e->getMessage(), 'Admin/WordTemplate/edit.html', $api);
            return;
        }

        if ($id == null) {
            Util::printError($this,ErrorCode::ERROR_PARAM_MISSING, '缺少参数', 'Admin/WordTemplate/edit.html', $api);
            return;
        }

        $wordTemplateDb = new WordTemplateDB();
        $result = $wordTemplateDb->getWordTemplateById($id);

        if ($api !== null) {
            $data['content'] = $result;
            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);
        } else {
            $this->assign('data', $result);
        }
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        parent::onRequest($actionName);
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