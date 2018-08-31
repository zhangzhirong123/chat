<?php

namespace app\index\controller;

use think\Request;
use app\index\controller\Upload;
use app\index\library\Login as lg;

class Register {

    public function index() {
        $nickname = input('post.nickname');
        //验证用户名的唯一性
        $return = lg::getInstance()->userOnly($nickname);
        if ($return) {
            return $return;
        }
        $name = input('post.name');
        //用户图像上传
        $obj = new Upload();
        $file = request()->file('file');
        $returnFile = json_decode($obj->index(true), true);
        if ($returnFile['status'] != 200) {
            return $returnFile;
        }
        $path_url = $returnFile['data']['url'];
        //入库
        $userData = [
            'name' => $name,
            'nickname' => $nickname,
            'path_url' => $path_url,
        ];
        $uid = \think\Db::table('db_member')->insertGetId($userData);
        if (!isset($uid)) {
            return $obj->ajaxReturn("100", "注册失败");
        }
        return $obj->ajaxReturn("200", "注册成功");
    }

}
