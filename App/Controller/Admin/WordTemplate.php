<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:22
 */

namespace App\Controller\Admin;


use App\DB\WordTemplateDB;
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

        $typeId = $params['typeId'] ?? null;

        $name = $params['name'] ?? null;

        $content = $params['content'] ?? null;

        $wordDb = new WordTemplateDB();

        if ($api !== null) {
            if ($typeId == null || $name == null || $content == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;
            }

            $result = $wordDb->addWordTemplate($typeId, $name, $content);

            $data['templateId'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            if ($typeId == null || $name == null || $content == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordTemplate/add.html');
                return;

            }

            $result = $wordDb->addWordTemplate($typeId, $name, $content);

            $this->assign('row', $result);

            //  $this->fetch('Admin/WordTemplate/add.html');

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

        $templateId = $params['templateId'] ?? null;

        $typeId = $params['typeId'] ?? null;

        $name = $params['name'] ?? null;

        $content = $params['content'] ?? null;

        $wordTemplateDb = new WordTemplateDB();

        if ($api !== null) {

            if ($templateId == null || $typeId == null || $name == null || $content == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');

                return;
            }

            $result = $wordTemplateDb->editWordTemplateById($templateId, $typeId, $name, $content);

            $data['updateRow'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            if ($templateId == null || $typeId == null || $name == null || $content == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordTemplate/edit.html');
                return;
            }

            $result = $wordTemplateDb->editWordTemplateById($templateId, $typeId, $name, $content);

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

        $templateId = $params['templateId'] ?? null;

        $api = $params['api'] ?? null;

        $wordTemplateDb = new WordTemplateDB();

        if ($api !== null) {

            if ($templateId == null) {

                Util::printResult($this->response(), ErrorCode::ERROR_PARAM_MISSING, '缺少参数');
                return;
            }

            $result = $wordTemplateDb->delWordTemplateById($templateId);

            $data['delRow'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            if ($templateId == null) {

                $this->assign('error', '缺少参数');

                $this->fetch('Admin/WordTemplate/del.html');
                return;
            }

            $result = $wordTemplateDb->delWordTemplateById($templateId);

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

        $pageIndex = $params['pageIndex'] ?? 1;

        $pageSize = $params['pageSize'] ?? 10;

        $api = $params['api'] ?? null;

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

            // $this->fetch('Admin/WordTemplate/index.html');

        }
    }

    /**
     * 通过模板Id获取模板内容
     */
    function getWordTemplateById()
    {
        $params = $this->request()->getRequestParam();

        $id = $params['id'] ?? null;

        $api = $params['api'] ?? null;

        $wordTemplateDb = new WordTemplateDB();

        if ($api !== null) {

            if ($id == null){

                Util::printResult($this->response(),ErrorCode::ERROR_PARAM_MISSING,'缺少参数');
                return;

            }

            $result = $wordTemplateDb->getWordTemplateById($id);

            $data['content'] = $result;

            Util::printResult($this->response(), ErrorCode::ERROR_SUCCESS, $data);

        } else {

            $result = $wordTemplateDb->getWordTemplateById($id);

            $this->assign('data', $result);
            // $this->fetch('Admin/WordTemplate/edit.html');
        }
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        $this->response()->withHeader('Access-Control-Allow-Origin', '*');
        $this->wordTemplateDB = Util::buildInstance('\App\DB\WordTemplateDB');
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