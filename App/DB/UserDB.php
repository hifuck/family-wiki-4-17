<?php
/**
 * 用户数据库类
 */
namespace App\DB;

class UserDB extends AbstractDB{

    var $TABLE = "gener_user";
    var $TABLE_HISTORY = "gener_loginHistory";
    var $TABLE_PROFILE = "gener_user_profile";

    /**
     * 根据id_token验证用户身份
     */
    public function validateUser($userId,$token,$device,$deviceCode = ''){

        if ($device == 3) {
            //微信登录会挤掉
            $sql = "SELECT token FROM $this->TABLE_HISTORY WHERE userId = '$userId'  AND device = '$device' AND success = '1' AND is_logout = '0'  order by id desc limit 0,1";

            $res = $this->uniqueResult($sql);          //这里只取了第一个
            $storageToken = $res['token'];

            if ($storageToken == $token) {
                return true;
            }
            return false;
        } else if ($device == 2) {
            //手机登录会挤掉
            $sql = "SELECT token FROM $this->TABLE_HISTORY WHERE userId = '$userId'  AND device = '$device' AND success = '1' AND is_logout = '0' order by id desc limit 0,1";

            $res = $this->uniqueResult($sql);          //这里只取了第一个
            $storageToken = $res['token'];

            if ($storageToken == $token) {
                return true;
            }
            return false;
        } else if ($device == 1) {
            //网页登录默认不会挤掉
            $sql = "SELECT count(id) FROM $this->TABLE_HISTORY WHERE userId = '$userId' AND success = '1' AND token = '$token'  AND is_logout = '0' ";

            $res = $this->uniqueResult($sql);

            if ($res['count(id)'] > 0) {
                return true;
            }
            return false;
        }
    }

}