<?php
/**
 * Created by PhpStorm.
 * User: xuliulei
 * Date: 18-4-17
 * Time: 上午10:55
 */

namespace App\DB;


class AdminUserDB extends AbstractDB
{

    public $TABLE_ADMIN_USER = 'wiki_admin_user';
    public $TABLE_ADMIN_TOKEN = 'wiki_admin_token';

    public function register($name, $password)
    {
        $sql = "INSERT INTO $this->TABLE_ADMIN_USER (name,password,create_time,update_time) VALUES (?,?,now(),now())";
        $params = [$name, $password];
        return $this->insert($sql, $params);
    }

    public function verifyUserIsExists($name)
    {
        $sql = "SELECT count('name') FROM $this->TABLE_ADMIN_USER WHERE name =?";
        $params = [$name];
        $result = $this->uniqueResult($sql, $params);
        if ($result["count('name')"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyUserLogin($name, $password)
    {
        $sql = "SELECT count('id') FROM $this->TABLE_ADMIN_USER WHERE name = ? AND password = ?";
        $params = [$name, $password];
        $result = $this->uniqueResult($sql, $params);
        if ($result["count('id')"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserIdByName($name)
    {
        $sql = "SELECT id FROM $this->TABLE_ADMIN_USER WHERE name = ?";
        $params = [$name];
        $result = $this->uniqueResult($sql, $params);
        return $result['id'];
    }

    public function addToken($userId, $token)
    {
        $sql = "INSERT INTO $this->TABLE_ADMIN_TOKEN (userId,token,is_delete,create_time,update_time) VALUES (?,?,0,now(),now())";
        $params = [$userId, $token];
        $result = $this->insert($sql, $params);
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function validateIdToken($userId, $token)
    {
        $sql = "SELECT id,userId,token FROM $this->TABLE_ADMIN_TOKEN WHERE is_delete = 0 AND userId = ? AND token = ? limit 0,1";
        $params = [$userId, $token];
        $result = $this->uniqueResult($sql, $params);
        if ($result !== null) {
            return $result;
        } else {
            return false;
        }
    }


}