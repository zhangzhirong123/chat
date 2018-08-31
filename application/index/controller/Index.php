<?php

namespace app\index\controller;

use Db;

class Index {

    public function index() {
//        return ajaxReturns('200', '上传成功');
//        echo 567;
//        $param = [
//            'uid' => 3,
//            'chatId' => 1,
//            'img' => '1.jpg',
//            'type' => 6,
//        ];
//
//        $data = Db::table('db_chat')
//                        //->alias('a')
//                        //->join('db_member w', 'a.send_id = w.uid', 'LEFT')
//                        ->where(function($query) use ($param) {
//                            $query->where('send_id', $param['uid'])
//                            ->where('accept_id', $param['chatId']);
//                        })
//                                ->limit(10)->order('time desc')->select();
//        echo \think\Db::table('db_chat')->getLastSql();
//        print_r($data);
    }

    public function login() {
//        echo 123;
    }

}
