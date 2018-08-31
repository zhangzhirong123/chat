<?php

namespace app\index\controller;

use app\index\library\Login as lg;

class Login {

    /**
     * 登录
     */
    public function index() {
        $name = $_GET['user'];
        $return = lg::getInstance()->login($name);
        return $return;
    }

    public function verLogin() {
        $t1 = microtime(true);
        $param = $_GET;
        $return = lg::getInstance()->verLogin($param, $_SERVER['QUERY_STRING']);
        //验证登录 链接失效
        $dataArr = [
            'data' => [ //数据
                'key' => config('redis.url') . $param['uid'],
                'token' => md5($_SERVER['QUERY_STRING']),
            ],
            'method' => 'setToken', //方法名
        ];
//        print_r($dataArr);
        $_POST['http_server']->task($dataArr);
        $t2 = microtime(true);
//        echo $t2-$t1;
        //Redis::getInstance()->set(self::URL . $param['uid'], md5($url));
        return $return;
    }

}
