<?php

namespace app\index\library;

use app\index\library\Upload;

class uploadImg extends Upload {

    use AjaxReturn;

    public $_dir = "./../public/static/static/img/";

    /**
     * 用户注册上传图像
     * @param type $file
     * @return type
     */
    public function upload($file) {
        $info = $file->move($this->_dir);
        if ($info) {
            $infos = $info->getInfo();
            // 源文件名
            $name = $infos['name'];
            $data = [
                'url' => $info->getSaveName(),
                'filename' => $name,
            ];
            return $this->ajaxReturn('200', '上传成功', $data);
        } else {
            // 上传失败获取错误信息
            return $this->ajaxReturn('100', '上次失败', $file->getError());
        }
    }
}
