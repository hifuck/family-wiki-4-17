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
     */
    public function validateUser($userId,$token,$device,$deviceCode = ''){

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
        $httpClient = new HttpClient($SSOConfig['SERVER_URL'],$SSOConfig['SERVER_PORT']);

        $params['action'] = 'account_action';
        $params['sub_action'] = 'checkSubSystemToken';
        $params['systemUrl'] = $systemUrl;
        $params['subToken'] = $token;

        $result = $httpClient->postWithoutHeader('/php/index.php',$params);

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