<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:26
 */

namespace App\DB;


class WordTemplateDB extends AbstractDB
{

    public $TABLE_TEMPLATE = 'wiki_word_template';

    /**
     * 添加分类词条模板
     * @param $typeId
     * @param $name
     * @param $content
     * @return bool
     */
    public function addWordTemplate(int $typeId, string $name, string $content)
    {
        $sql = "INSERT INTO $this->TABLE_TEMPLATE (typeId,name,content,createTime,updateTime) VALUES (?,?,?,now(),now())";
        $params = [$typeId, $name, $content];
        return $this->insert($sql, $params);
    }

    /**
     * 编辑模板
     * @param $templateId
     * @param $typeId
     * @param $name
     * @param $content
     * @return bool
     */
    public function editWordTemplateById(int $templateId, int $typeId, string $name, string $content)
    {
        $sql = "UPDATE $this->TABLE_TEMPLATE SET typeId=?,name=?,content=?,updateTime=now() WHERE id=?";
        $params = [$typeId, $name, $content, $templateId];
        return $this->update($sql, $params);
    }

    /**
     * 根据模板ID删除模板
     * @param $templateId
     * @return bool
     */
    public function delWordTemplateById(int $templateId)
    {
        $sql = "DELETE FROM $this->TABLE_TEMPLATE WHERE id=?";
        $params = [$templateId];
        return $this->update($sql, $params);
    }

    /**
     * 分页获取模板
     * @param $pageIndex
     * @param $pageSize
     * @return bool
     */
    public function getWordTemplatePaging(int $pageIndex,int $pageSize)
    {
        $start = ($pageIndex - 1) * $pageSize;
        $sql = "SELECT  id,typeId,name,content,createTime,updateTime FROM $this->TABLE_TEMPLATE ORDER BY id DESC LIMIT $start,$pageSize";
        return $this->query($sql);
    }

    /**
     * 多少条数据
     * @return mixed
     */
    public function countWordTemplate()
    {
        $sql = "SELECT count('id') FROM $this->TABLE_TEMPLATE";
        $result = $this->uniqueResult($sql);
        return $result["count('id')"];
    }

    /**
     *
     * @param $id
     * @return null
     */
    public function getWordTemplateById(int $id)
    {
        $sql = "SELECT id,typeId,name,content,createTime,updateTime FROM $this->TABLE_TEMPLATE WHERE id=?";
        $params = [$id];
        return $this->uniqueResult($sql, $params);
    }
}