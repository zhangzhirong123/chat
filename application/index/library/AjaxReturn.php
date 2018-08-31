<?php

namespace app\index\library;

trait AjaxReturn {

    /**
     * 接口返回值
     * @param type $status
     * @param type $msg
     * @param type $data
     * @return type
     */
    public function ajaxReturn($status, $msg, $data = []) {
        $returnData = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        ];
        return json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

}
