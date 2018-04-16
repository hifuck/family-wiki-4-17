<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-3
 * Time: 下午5:24
 */

namespace App\DB;


class WordTypeDB extends AbstractDB
{

    public $TABLE_TYPE = 'wiki_word_type';

    /**
     * 添加词条分类
     * @param string $type
     * @param int $parentId
     * @param int $depth
     * @return bool
     * @internal param string $name
     */
    public function addWordType(string $type, int $parentId, int $depth)
    {
        $sql = "INSERT INTO $this->TABLE_TYPE (type,parentId,depth,createTime,updateTime) VALUES (?,?,?,now(),now())";
        $params = array($type, $parentId, $depth);
        $result = $this->insert($sql, $params);
        return $result;
    }

    /**
     * 分类是否已经存在 //同级别下
     * @param $type
     * @param $parentId
     * @return null
     */
    public function wordTypeIsExist(string $type, int $parentId)
    {
        $sql = "SELECT count('type') FROM $this->TABLE_TYPE WHERE type = ? AND parentId = ?";
        $params = array($type, $parentId);
        $result = $this->uniqueResult($sql, $params);
        if ($result["count('type')"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 编辑分类
     * @param $type //分类名字
     * @param $parentId //父级id
     * @param $depth // 深度
     * @param $typeId
     * @return bool
     * @internal param 分类Id $id
     */
    public function editWordType(string $type, int $parentId, int $depth, int $typeId)
    {
        $sql = "UPDATE $this->TABLE_TYPE SET type = ? ,parentId = ? ,depth = ? ,updateTime = now() WHERE id = ? ";
        $params = [$type, $parentId, $depth, $typeId];
        return $this->update($sql, $params);
    }


    public function checkIsParent(int $typeId)
    {
        $sql = "SELECT count('id') FROM $this->TABLE_TYPE WHERE parentId = ? ";
        $params = [$typeId];
        $result = $this->uniqueResult($sql, $params);
        if ($result["count('id')"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function del(int $typeId)
    {
        $sql = "DELETE FROM $this->TABLE_TYPE WHERE id=?";
        $params = [$typeId];
        return $this->update($sql, $params);

    }


    /**
     * 删除分类及该分类下的子分类
     * @param $typeId
     * @return null
     */
    public function delWordType(int $typeId)
    {

        $sql = "DELETE FROM $this->TABLE_TYPE WHERE id=?";
        $params = [$typeId];
        $delRow = $this->update($sql, $params);
        $delArr = array();
        array_push($delArr, $delRow);
        if ($delRow > 0) {
            $sql = "DELETE FROM $this->TABLE_TYPE WHERE parentId = ?";
            $params = [$typeId];
            $delRows = $this->update($sql, $params);
            array_push($delArr, $delRows);
        } else {
            array_push($delArr, 0);
        }

        $data['delRow'] = $delArr[0];
        $data['delChildRows'] = $delArr[1];
        return $data;
    }

    /**
     * c
     * @param $typeId
     * @return null
     */
    public function getWordTypeById(int $typeId)
    {
        $sql = "SELECT id,type,parentId,depth,createTime,updateTime FROM $this->TABLE_TYPE WHERE id=?";
        $params = [$typeId];
        return $this->uniqueResult($sql, $params);
    }

    /**
     * 获取all词条分类
     * @param $pageIndex
     * @param $pageSize
     * @return bool
     */
    public function getAllWordType()
    {
        $sql = "SELECT id,type,parentId,depth,createTime,updateTime FROM $this->TABLE_TYPE";
        return $this->query($sql);
    }

    /**
     * 总共有多少个词条分类
     * @return mixed
     */
    public function countWordType()
    {
        $sql = "SELECT count('id') FROM $this->TABLE_TYPE ";
        $result = $this->uniqueResult($sql);
        return $result["count('id')"];
    }

    /**
     * 分页获取一级分类列表
     * @param $pageIndex
     * @param $pageSize
     * @return bool
     */
    public function getTopWordTypePaging(int $pageIndex, int $pageSize)
    {
        $start = ($pageIndex - 1) * $pageSize;
        $sql = "SELECT id,type,parentId,depth,createTime,updateTime FROM $this->TABLE_TYPE WHERE parentId = 0 LIMIT $start,$pageSize";
        return $this->query($sql);
    }

    /**
     * 总共多少条一级分类
     * @return mixed
     */
    public function countTopWordType()
    {
        $sql = "SELECT count('id') FROM $this->TABLE_TYPE WHERE parentId = 0 ";
        $result = $this->uniqueResult($sql);
        return $result["count('id')"];
    }

    /**
     * 根据分类Id获取子分类
     * @param $typeId
     * @return null
     */
    public function getChildWordTypeListById(int $typeId)
    {
        $sql = "SELECT id,type,parentId,depth,createTime,updateTime FROM $this->TABLE_TYPE WHERE parentId = ?";
        $params = [$typeId];
        return $this->query($sql, $params);
    }
}