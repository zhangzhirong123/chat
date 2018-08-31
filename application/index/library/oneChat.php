<?php

namespace app\index\library;

use app\index\library\Chat;

class oneChat extends Chat {

    /**
     * 单人聊天入库
     * @param type $data
     */
    public function Add($param) {
        $data = [
            'send_id' => $param['uid'],
            'content' => $param['content'],
            'time' => $param['stime'],
            'accept_id' => $param['ChatId'],
        ];
        try {
            $id = \think\Db::table('db_chat')->insertGetId($data);
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        if (isset($param['url'])) {
            $fileData = [
                'file_url' => $param['url'],
                'status' => 1,
                'chat_id' => $id
            ];
            \think\Db::table('db_file')->insert($fileData);
        }
    }

    /**
     * 单人聊天记录
     * @param type $param
     */
    public function select($param) {
        $data = $dataAll = [];
        $query = new \think\db\Query;
        $data = \think\Db::table('db_chat')
                        ->alias('a')
                        ->join('db_member w', 'a.send_id = w.uid', 'LEFT')
                        ->where(function($query) use ($param) {
                            $query->where(['send_id' => $param['uid'], 'accept_id' => $param['chatId']]);
                        })
                        ->whereOr(function($query) use ($param) {
                            $query->where(['send_id' => $param['chatId'], 'accept_id' => $param['uid']]);
                        })
                        ->limit(10)->order('time desc')->select();
        if ($data == false) {
            return [];
        }
        $dataAll = $this->selectFile($data);
        $this->update($param['uid'], $param['chatId']);
        return $dataAll;
    }

    /**
     * 查询是否有对应的文件
     * @param type $data
     */
    public function selectFile($data) {
        $fileData = [];
        foreach ($data as $k => $v) {
            $fileData = \think\Db::table('db_file')->where(['chat_id' => $v['id'], 'status' => 1])->value('file_url');
            if ($fileData) {
                $data[$k]['url'] = $fileData;
            } else {
                $data[$k]['url'] = 0;
            }
        }
        return $data;
    }

    /**
     * 修改状态
     * @param type $uid
     * @param type $chatId
     * @return type
     */
    public function update($uid, $chatId) {
        \think\Db::table('db_chat')->where(['accept_id' => $uid, 'send_id' => $chatId])->setField('status', '1');
    }

    /**
     * 查询用户
     * @param type $data
     */
    public function search($param) {
        $data = \think\Db::table("db_member")->where('name|nickname', 'like', "%{$param}%")->select();
//        echo \think\Db::table("db_member")->getLastSql();
        if (!$data) {
            return [];
        }
        return $data;
    }

    /**
     * 添加好友
     * @param type $param
     */
    public function addUser($param) {
        $data = [
            'uid' => $param['uid'],
            'sid' => $param['friendsId'],
            'status' => 2
        ];
        \think\Db::table('db_rel')->insertGetId($data);
    }

    /**
     * 处理好友申请
     */
    public function updateStatus($param) {
        $where = [
            'uid' => $param['frinsdId'],
            'sid' => $param['uid'],
        ];

        try {
            \think\Db::table('db_rel')->where($where)->setField("status", $param['status']);
            if ($param['status'] == 1) {
                $data = [
                    'uid' => $param['uid'],
                    'sid' => $param['frinsdId'],
                    'status' => 1
                ];
                \think\Db::table('db_rel')->insertGetId($data);
            }
        } catch (Exception $ex) {
            
        }
    }

}
