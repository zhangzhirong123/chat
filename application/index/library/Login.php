<?php

namespace app\index\library;

use app\index\library\Predis;
use app\index\library\Redis;

class Login {

    private static $_instance = null;
    private static $db = null;

    const TOEKNS = "#$%*&456";
    const URL = "url_";
    const USER = "user_";

    private function __construct() {
        
    }

    //连接数据库
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //登录成功存储
    public function login($name) {
        $t1 = microtime(true);
        if (!$name) {
            return $this->ajaxReturn('100', '用户名为空');
        }
        $data = \think\Db::table('db_member')->where(['nickname' => $name])->find();
        if ($data == false) {
            return $this->ajaxReturn('100', '用户名不存在');
        }
        //生成token
        $token = md5(self::TOEKNS . $data['nickname']);
        $dataArr = [
            'data' => [ //数据
                'key' => self::USER . $data['uid'],
                'token' => $token,
            ],
            'method' => 'setToken', //方法名
        ];
        //存储在线用户
        $dataUser = [
            'data' => [ //数据
                'key' => config('redis.user'),
                'data' => base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE)),
            ],
            'method' => "onlineUsers", //方法
        ];
        $_POST['http_server']->task($dataArr);
        $_POST['http_server']->task($dataUser);
        //Predis::getInstance()->set(self::USER . $data['uid'], $token);
        $t2 = microtime(true);
//        echo $t2 - $t1;
        return $this->ajaxReturn('200', '登录成功', ['token' => $token, 'user' => $data]);
    }

    //接口返回值
    public function ajaxReturn($status, $msg, $data = []) {
        $returnData = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        ];
        return json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 验证登录
     * @param type $param
     */
    public function verLogin($param, $url) {
        if (!$param) {
            return $this->ajaxReturn('100', '用户名没登录');
        }
        if (Predis::getInstance()->get(self::URL . $param['uid']) == md5($url)) {
            return $this->ajaxReturn('100', '链接已失效');
        }
        $data = "";
        $token = Predis::getInstance()->get(self::USER . $param['uid']);
//        print_r(str_split($param['sign']));
        //截取token值
        foreach (str_split($param['sign']) as $v) {
            $data.=substr($token, $v, 1);
        }
//        echo $data;
        if (md5($data) == $param['token']) {
            return $this->ajaxReturn('200', '已登录成功');
        }
        return $this->ajaxReturn('100', '未登录');
    }

    /**
     * 验证用户唯一性
     * @param type $nickname
     */
    public function userOnly($nickname) {
        $return = \think\Db::table('db_member')->where(['nickname' => $nickname])->value("uid");
        if ($return) {
            return $this->ajaxReturn('100', '用户已存在');
        }
    }

}
