<?php
/**
 * 用户数据库类
 */
namespace App\DB;

use App\Utils\HttpClient;
use Conf\Config;
use App\Model\User;

class UserDB extends AbstractDB{

    public $TABLE = "wiki_user_token";

    /**
     * 根据id_token验证用户身份
     * @param $userId 用户id
     * @param $token  令牌
     * @param $username 用户名
     * @return bool true代表有效，false代表无效
     */
    public function validateUser($userId,$token,$username){
        $sql = "SELECT id FROM $this->TABLE WHERE 
        userId = '$userId' AND token = '$token' 
        AND username = '$username' AND isDelete = '0' limit 0,1";
        $result = $this->query($sql);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 从数据库中删除token
     * @param $userId 用户id
     * @param $token  令牌
     * @return int 更新的记录数
     */
    public function deleteToken($userId, $token) {
        $sql = "UPDATE $this->TABLE SET isDelete = '1' WHERE userId = '$userId' AND token = '$token' ";
        return $this->update($sql);
    }

    public function addUserToken(User $user) {
        $sql = "INSERT INTO $this->TABLE
                (userId,token,username,nickname,photo,gender,createTime,updateTime)
                VALUES
                (?,?,?,?,?,?,now(),now())";

        $params = array($user->userId,$user->token,$user->username,$user->nickname,$user->photo,$user->gender);

        return $this->insert($sql,$params);
    }

    /**
     * 检查token是否正确
     * 
     * @param $token 令牌
     * @param $systemUrl 系统url
     * @return User 用户资料
     */
    public function checkToken($token,$systemUrl) {
        $SSOConfig = Config::getInstance()->getConf('SSO');
        $httpClient = new HttpClient();

        $params['action'] = 'account_action';
        $params['sub_action'] = 'checkSubSystemToken';
        $params['systemUrl'] = $systemUrl;
        $params['subToken'] = $token;

        $result = $httpClient->postWithoutHeader($SSOConfig['SERVER_URL'].':'.$SSOConfig['SERVER_PORT'].'/php/index.php',$params);

        if ($result != false) {
            $result = \json_decode($result,true);

            if ($result['errorCode'] === '0') {
                // token正确
                $userData = $result['data']['user'];
                $user = new User();
                $user->userId = $userData['userId'];
                $user->token = $token;
                $user->username = $userData['username'];
                $user->nickname = $userData['nickname'];
                $user->photo = $userData['photo'];
                $user->gender = $userData['gender'];
                $this->addUserToken($user);

                return $user;
            } else {
                // token错误
                return false;
            }
        } else {
            return false;
        }
    }

}