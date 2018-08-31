<?php

namespace app\index\library;

use app\index\library\Chat;

class manyChat extends Chat {

    /**
     * 多人聊天入库
     * @param type $data
     */
    public function Add($param) {
        $data = [
            'send_id' => $param['uid'],
            'group_desc' => $param['content'],
            'group_time' => $param['stime'],
        ];
        try {
            $id = \think\Db::table('db_group')->insertGetId($data);
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        if (isset($param['url'])) {
            $fileData = [
                'file_url' => $param['url'],
                'status' => 0,
                'chat_id' => $id
            ];
            \think\Db::table('db_file')->insert($fileData);
        }
    }

    /**
     * 多人聊天记录
     */
    public function select($param) {
        $data = \think\Db::table('db_group')
                        ->alias('a')
                        ->join('db_member w', 'a.send_id = w.uid', 'LEFT')
                        ->limit(10)->order('group_time desc')->select();
        if (!isset($data)) {
            return [];
        }
        $dataAll = $this->selectFile($data);
        return $dataAll;
    }

    /**
     * 查询是否有对应的文件
     * @param type $data
     */
    public function selectFile($data) {
        $fileData = [];
        foreach ($data as $k => $v) {
            $fileData = \think\Db::table('db_file')->where(['chat_id' => $v['id'], 'status' => 0])->value('file_url');
            if ($fileData) {
                $data[$k]['url'] = $fileData;
            } else {
                $data[$k]['url'] = 0;
            }
        }
        return $data;
    }

    /**
     * 查询用户
     * @param type $data
     */
    public function search($param) {
        
    }

    /**
     * 入群申请
     * @param type $param
     */
    public function addUser($param) {
        
    }

    /**
     * 处理入群申请
     */
    public function updateStatus($param) {
        
    }

}
