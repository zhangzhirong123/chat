<?php

namespace app\index\controller;

use app\index\library\chatAdd;

class Search {

    use \app\index\library\AjaxReturn;

    /**
     * 搜索用户
     * @return type
     */
    public function index() {
        $name = input("get.name");
        if (!isset($name)) {
            return [];
        }
        if (input("get.mark") == 0) {
            $obj = new chatAdd("app\index\library\manyChat");
        } else {
            $obj = new chatAdd("app\index\library\oneChat");
        }
        $data = $obj->search($name);
        return $this->ajaxReturn('200', '查询成功', $data);
    }

//    /**
//     * 接口返回值
//     * @param type $status
//     * @param type $msg
//     * @param type $data
//     * @return type
//     */
//    public function ajaxReturn($status, $msg, $data = []) {
//        $returnData = [
//            'status' => $status,
//            'msg' => $msg,
//            'data' => $data,
//        ];
//        return json_encode($returnData, JSON_UNESCAPED_UNICODE);
//    }

    /**
     * 添加好友
     * @return type
     */
    public function addFriends() {
        $param = input("get.");
        if (!isset($param['uid']) || !isset($param['friendsId'])) {
            return $this->ajaxReturn('100', '非法请求');
        }
        //判断是否已是好友
        $dataUser = \think\Db::table("db_rel")->where(['uid' => $param['uid'], 'sid' => $param['friendsId']])->find();
        if (isset($dataUser)) {
            if ($dataUser['status'] == 2) {
                return $this->ajaxReturn('100', '请求已发送');
            }
            if ($dataUser['status'] == 1) {
                return $this->ajaxReturn('100', '你们已是好友');
            }
        }
        //申请请求
        $dataArr = [
            'data' => $param,
            'method' => 'appFriends', //方法名
        ];
        $_POST['http_server']->task($dataArr);
        return $this->ajaxReturn('200', '请求发送成功');
    }

    /**
     * 选择是否同意
     * @return type
     */
    public function choice() {
        $param = input("get.");

        if (!isset($param['uid']) || !isset($param['frinsdId'])) {
            return $this->ajaxReturn('100', '非法请求');
        }
        //申请请求
        $dataArr = [
            'data' => $param,
            'method' => 'choiceStatus', //方法名
        ];
        $_POST['http_server']->task($dataArr);
        return $this->ajaxReturn('200', '请求发送成功');
    }

}
