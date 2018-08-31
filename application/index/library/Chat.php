<?php

namespace app\index\library;

abstract class Chat {
    /* 入库聊天 */

    abstract function Add($data);
    /* 聊天记录 */

    abstract function select($data);
    /* 查询用户 */

    abstract function search($data);
    /*     * 添加好友 */

    abstract function addUser($data);
    /* 操作请求 */

    abstract function updateStatus($data);
}
