<?php

namespace app\index\controller;

use app\index\library\Predis;
use app\index\library\User;
use app\index\library\chatAdd;
use app\index\library\oneChat;

class Task {

    /**
     * 存储token
     * @param type $data
     */
    public function setToken($data, $serv = false) {
        Predis::getInstance()->set($data['key'], $data['token']);
    }

    /**
     * 存储在线用户
     * @param type $data
     */
    public function onlineUsers($data, $serv = false) {
        Predis::getInstance()->sAdd($data['key'], $data['data']);
        $this->listUser($serv);
        $this->processNews(json_decode(base64_decode($data['data']), true)['uid'], $serv);
    }

    /**
     * 获取在线用户的好友及是否在线
     * @param type $serv
     */
    public function listUser($serv = false) {
        $data = Predis::getInstance()->sMembers(config('redis.user'));
        $info = [];
        $info = [
            'type' => 5,
            'username' => User::getInstance()->Userselect($data),
        ];
//        print_r($info);
        foreach ($serv->ports[1]->connections as $fd) {
            $serv->push($fd, json_encode($info));
//            $server->push($fd, json_encode($chatAll));
        }
    }

    /**
     * 聊天入库
     * @param type $data
     */
    public function chatAdd($data, $serv = false) {
        $chatFd = $chatData = $chatAll = $fds = [];
        $chatData = json_decode($data->data, true);
        $chatFd = $serv->ports[1]->connections;
        foreach ($chatFd as $k => $v) {
            $chatAll[$k] = $v;
        }
        if ($chatData['ChatId'] == 0) {
            $obj = new chatAdd("app\index\library\manyChat");
        } else {
            $obj = new chatAdd("app\index\library\oneChat");
            $fds = Predis::getInstance()->sMembers(config('redis.chatId') . $chatData['ChatId']);
            $chatAll = array_intersect($fds, $chatAll);
        }
        $obj->add($chatData);
        $this->listUser($serv);
//        print_r($obj)
        $this->processNews($chatData['uid'], $serv);
        foreach ($chatAll as $fd) {
            if ($data->fd != $fd) {
                $serv->push($fd, $data->data);
            }
        }
    }

    /**
     * 查询聊天列表
     * @param type $data
     * @param type $serv
     */
    public function chatSelect($data, $serv = false) {
        $chatData = $dataAll = [];
        $chatData = json_decode($data->data, true);
//        print_r($chatData);
        if ($chatData['chatId'] == 0) {
            $obj = new chatAdd("app\index\library\manyChat");
        } else {
            $obj = new chatAdd("app\index\library\oneChat");
        }
        $dataAll = [
            "type" => 6,
            'userInfo' => $chatData,
            "data" => $obj->select($chatData),
        ];
//        print_r($chatData);
        $this->listUser($serv);
        $this->processNews($chatData['uid'], $serv);
        $serv->push($data->fd, json_encode($dataAll));
    }

    /**
     * 修改聊天状态
     * @param type $data
     * @param type $serv
     */
    public function chatStatus($data, $serv = false) {
        $obj = new oneChat();
        $obj->update($data['uid'], $data['chatId']);
        $this->listUser($serv);
        $this->processNews($data['uid'], $serv);
    }

    /**
     * 添加好友请求
     * @param type $data
     * @param type $serv
     */
    public function appFriends($data, $serv = false) {
        if ($data['mark'] == 0) {
            $obj = new chatAdd("app\index\library\manyChat");
        } else {
            $obj = new chatAdd("app\index\library\oneChat");
        }
        $obj->addUser($data);
        Predis::getInstance()->sAdd(config('redis.news') . $data['friendsId'], $data['uid']);
        $this->processNews($data['friendsId'], $serv);
    }

    /**
     * 通知消息获取
     * @param type $uid
     */
    public function processNews($uid, $serv = false) {
        $data = Predis::getInstance()->sMembers(config('redis.news') . $uid);
        $chatAll = [];
        if (isset($data)) {
            //获取用户信息
            foreach ($data as $k => $v) {
                $data[$k] = \think\Db::table('db_member')->where(['uid' => $v])->find();
                $data[$k]['status'] = \think\Db::table('db_rel')->where(['uid' => $v, 'sid' => $uid])->value('status');
            }

            $count = 0;
            if (isset(array_count_values(array_column($data, "status"))[2])) {
                $count = 2;
            }
            $info = [
                'type' => 10,
                'news' => $data,
                'count' => $count,
            ];
            $chatFd = $serv->ports[1]->connections;
            foreach ($chatFd as $k => $v) {
                $chatAll[$k] = $v;
            }
            $fds = Predis::getInstance()->sMembers(config('redis.chatId') . $uid);
            $chatAll = array_intersect($fds, $chatAll);
            foreach ($chatAll as $fd) {
                $serv->push($fd, json_encode($info));
            }
        }
    }

    /**
     * 修改状态
     * @param type $data
     * @param type $serv
     */
    public function choiceStatus($data, $serv = false) {
        if ($data['mark'] == 0) {
            $obj = new chatAdd("app\index\library\manyChat");
        } else {
            $obj = new chatAdd("app\index\library\oneChat");
        }
        $chatFd = $serv->ports[1]->connections;
        foreach ($chatFd as $k => $v) {
            $chatAll[$k] = $v;
        }
        $obj->updateStatus($data);
        $this->listUser($serv);
        $this->processNews($data['uid'], $serv);
    }

    /**
     * 
     * @param type $param
     */
    public function fileMerge($param, $serv = false) {
        $type = "." . substr($param['name'], strrpos($param['name'], '.') + 1);
//        echo $type;
//        print_r($param);
        $fp = fopen($param['filePath'] . $type, "w+");
        for ($i = 0; $i <= $param['chunk']; $i++) {
            $handle = fopen(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'] . "/" . $i . $type, "rb");
            fwrite($fp, fread($handle, filesize(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'] . "/" . $i . $type)));
            unlink(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'] . "/" . $i . $type);
            fclose($handle);
        }
        if (!is_file(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'] . "/")) {
            rmdir(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'] . "/");
        }
    }

}
