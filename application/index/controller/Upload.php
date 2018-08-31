<?php

namespace app\index\controller;

use app\index\library\uploadFile;
use app\index\library\uploadImg;

class Upload {

    /**
     * 上传文件
     * @return type
     */
    public function index($param = false) {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            if ($param != false) {
                $obj = new uploadImg();
                //  $info = $file->move('./../public/static/static/img/');
            } else {
                $obj = new uploadFile();
            }
//            print_r($obj);
            $return = $obj->upload($file);
            return $return;
//            print_r($return);
        }
    }

    /**
     * 下载文件
     */
    public function download() {
        echo "<pre>";
        print_r($_SERVER);die;
        $file = './static/upload/' . $_GET['url'];
        $this->down_file($file, $_GET['fileName']);
    }

    /**
     * 文件下载
     * @param type $filename
     * @param type $fileName
     * @param type $allowDownExt
     */
    public function down_file($filename, $fileName, $allowDownExt = array('jpg', 'jpeg', 'gif', 'rar', 'zip', 'png', 'txt', 'html', 'mp4')) {
        //下面是输出下载;
        $file = basename($fileName); //获取文件名字 
        $handle = fopen($filename, "rb");
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = rawurlencode($fileName);
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header("Content-Disposition: attachment; filename*=\"utf8''" . $file . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file . '"');
        }
        header("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($filename)); // 告诉浏览器，文件大小
//        @readfile($filename); //输出文件;
        while (!feof($handle)) {
            $contents = fread($handle, 8192);
            echo $contents;
            @ob_flush();  //把数据从PHP的缓冲中释放出来
            flush();      //把被释放出来的数据发送到浏览器
        }
        fclose($handle);
    }

}
