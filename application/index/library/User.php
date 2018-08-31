<?php

namespace app\index\library;

class User {

    private static $_instance = null;

    private function __construct() {
        
    }

    //实例化类
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 好友列表信息
     * @param type $data
     */
    public function Userselect($data) {
        $user = $info = [];
        foreach ($data as $k => $v) {
            $user = json_decode(base64_decode($v), true);
            $dataUid[$k] = $user['uid'];
            $info[$k] = $user;
        }

        foreach ($info as $k => $v) {
            $data = $this->userList($v['uid']);
            if ($data) {
                foreach ($data as $kk => $vv) {
                    if (in_array($vv['uid'], $dataUid)) {
                        $data[$kk]['status'] = 1;
                    } else {
                        $data[$kk]['status'] = 0;
                    }
                }
            }
            $info[$k]['rel'] = $data;
        }
        return $info;
    }

    /**
     * 好友列表及未读消息
     * @param type $uid
     * @return int
     */
    public function userList($uid) {
        //获取好友列表
        $dataFriends = $dataCount = [];
        $dataFriends = \think\Db::table('db_rel')
                ->alias('a')
                ->field("w.uid,w.name,w.path_url")
                ->join('db_member w', 'a.sid = w.uid')
                ->where(['a.status' => 1, 'a.uid' => $uid])
                ->select();
        //查询有多少未读消息
        $dataCount = \think\Db::table('db_chat')
                ->field('count(id) as count,send_id')
                ->where(['status' => 0, 'accept_id' => $uid])
                ->group('send_id')
                ->select();
        $count = [];
        foreach ($dataCount as $k => $v) {
            $count[$v['send_id']] = $v['count'];
        }
        foreach ($dataFriends as $k => $v) {
            if (array_key_exists($v['uid'], $count)) {
                $dataFriends[$k]['count'] = $count[$v['uid']];
            } else {
                $dataFriends[$k]['count'] = 0;
            }
        }
        return $dataFriends;
    }

}
