<?php

namespace app\index\library;

class Redis {

    private static $_instance = null;
    private static $_redis = null;

    const HOST = "127.0.0.1";
    const PORT = "6379";
    const TIME = '300';

    private function __construct() {

        self::$_redis = new \Swoole\Coroutine\Redis();
        self::$_redis->connect(self::HOST, self::PORT);
    }

    /*
     * 单例模式
     * 实例化连接redis
     */

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * redis操作
     * @param type $name
     * @param type $data
     * @return type
     */
    public function __call($name, $data) {
        if (count($data) > 1) {
            self::$_redis->$name($data['0'], $data['1']);
            return self::$_redis->EXPIRE($data['0'], self::TIME);
        }
        return self::$_redis->$name($data['0']);
    }

}
