<?php

namespace app\index\library;

use app\index\library\Upload;

class uploadFile extends Upload {

    use AjaxReturn;
//    public $_dir = "./../public/static/upload/";

    /**
     * 文件上传
     * @param type $file
     */
    public function upload($file) {
        $param = input('post.');
        unset($param['http_server']);
        $path = date("Ymd") . "/" . uniqid();
        $param['filePath'] = config("upload.fileDir") . $path;
//        print_r($param);
        if (!isset($param['chunks'])) {
            $info = $file->move(config("upload.fileDir"));
            $data = [
                'url' => $info->getSaveName(),
                'filename' => $param['name'],
            ];
            return $this->ajaxReturn('200', '上传成功', $data);
        }
        $info = $file->move(config("upload.fileDir") . date("Ymd") . "/" . $param['guid'], $param['chunk']);
        if ($info) {
            if ($param['chunks'] - $param['chunk'] == 1) {
                $dataArr = [
                    'data' => $param,
                    'method' => 'fileMerge', //方法名
                ];
                $_POST['http_server']->task($dataArr);
                $data = [
                    'url' => $path . "." . substr($param['name'], strrpos($param['name'], '.') + 1),
                    'filename' => $param['name'],
                ];
                return $this->ajaxReturn('200', '上传成功', $data);
            }
        }
    }

}
