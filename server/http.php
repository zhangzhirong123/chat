<?php

class http {

    CONST HSOT = '0.0.0.0';
    CONST PORT = 9501;
    CONST PORTS = 9503;
    CONST PORTSS = 9504;
    CONST CHATID = 'chat_';
    CONST PROCESS = 'process_';
    CONST USER = 'onlineUser';
    CONST USERNAME = 'username_';

    public $http;
    public $Agent;

    /*
     * 实例化服务
     */

    public function __construct() {
        //实例化服务
        $this->http = new swoole_websocket_server(self::HSOT, self::PORT);
        $this->http->listen(self::HSOT, self::PORTS, SWOOLE_SOCK_TCP);
        $this->http->listen(self::HSOT, self::PORTSS, SWOOLE_SOCK_TCP);
        $this->http->set([
            // 开启静态资源请求
            'enable_static_handler' => true,
            'document_root' => __DIR__ . '/../public/static',
            'worker_num' => 5,
            'task_worker_num' => 5,
        ]);
        $this->http->on('start', array($this, 'onStart'));
        $this->http->on('workerstart', array($this, 'onWorkerStart'));
        $this->http->on('open', array($this, 'onOpen'));
        $this->http->on('message', array($this, 'onMessage'));
        $this->http->on('request', array($this, 'onRrequest'));
        $this->http->on('task', array($this, 'onTask'));
        $this->http->on('finish', array($this, 'onFinish'));
        $this->http->on('close', array($this, 'onClose'));

        $this->http->start();
    }

    /**
     *  在进程开启之前加载文件
     * @param type $serv
     * @param type $worker_id
     */
    public function onWorkerStart($serv, $worker_id) {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * 
     * @param type $server
     * @param type $request
     */
    public function onOpen($server, $request) {
//        ECHO count($server->connections) . PHP_EOL;
        //\Handle\Redis::getInstance()->sAdd('process', $request->fd);
        ///  echo "server: handshake success with fd{}\n";
        //用户存储
        //print_R($server);
    }
    /**
     * 监听主进程
     */
    public function onStart(){
//        echo $this->http->start();
        swoole_set_process_name("chat-master");
        //$this->http->name("php7.2 http.php: huanggang");
    }

    /**
     * 
     * @param type $server
     * @param type $frame
     */
    public function onMessage($server, $frame) {
        $data = json_decode($frame->data, true);
        $dataInfo = $info = $dataFds = [];
//        //登录
        if ($data['type'] == 1) {
            //存储在同一浏览器上,开的进程数
            app\index\library\Redis::getInstance()->sAdd(self::CHATID . $data['data']['uid'], $frame->fd);
            //存储每个进程对应的键值
            app\index\library\Redis::getInstance()->set(self::PROCESS . $frame->fd, self::CHATID . $data['data']['uid']);
            $dataChat = [
                'data' => $frame,
                'method' => "chatSelect",
            ];
            $this->http->task($dataChat);
        }
        //发送消息
        if ($data['type'] == 3) {
            $dataChat = [
                'data' => $frame,
                'method' => "chatAdd",
            ];
            $this->http->task($dataChat);
        }
        //退出登录
        if ($data['type'] == 4) {
            $userData = base64_encode(json_encode($data['data'], JSON_UNESCAPED_UNICODE));
            //退出 删除用户信息
            app\index\library\Redis::getInstance()->sRem(self::USER, $userData);
            //删除用户进程id
            app\index\library\Redis::getInstance()->del(self::CHATID, $data['data']['uid']);
        }
        //获取单独用户聊天记录
        if ($data['type'] == 6) {
            $dataChat = [
                'data' => $frame,
                'method' => "chatSelect",
            ];
            $this->http->task($dataChat);
        }
        //修改状态
        if ($data['type'] == 7) {
            $dataStatus = [
                'data' => $data,
                'method' => 'chatStatus',
            ];
            $this->http->task($dataStatus);
        }
    }

    /**
     * 处理回调函数
     * @param type $request
     * @param type $response
     */
    public function onRrequest($request, $response) {
        // 把swoole接收的信息转换为thinkphp可识别的
        $_SERVER = [];
        if (isset($request->server)) {
            foreach ($request->server as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }

        if (isset($request->header)) {
            foreach ($request->header as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }

        // swoole对于超全局数组：$_SERVER、$_GET、$_POST、define不会释放
        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $key => $value) {
                $_GET[$key] = $value;
            }
        }

        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $key => $value) {
                $_POST[$key] = $value;
            }
        }
        $_COOKIE = [];
        if (isset($request->cookie)) {
            foreach ($request->cookie as $key => $value) {
                $_COOKIE[$key] = $value;
            }
        }
        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $key => $value) {
                $_FILES[$key] = $value;
            }
        }
        if ($_SERVER['PATH_INFO'] == "/favicon.ico") {
            return $response->end();
        }

        $_POST['http_server'] = $this->http;
        ob_start();
        try {
            think\Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
                    ->run()
                    ->send();
        } catch (\Exception $e) {
            $e->getMessage();
        }
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * task处理结果回调
     * @param type $serv
     * @param type $task_id
     * @param type $data
     */
    public function onFinish($serv, $task_id, $data) {
//        print_r($data);
//        $serv->finish($data);
    }

    /**
     *  work进程任务出来  异步处理
     * @param type $serv
     * @param type $task_id
     * @param type $src_worker_id
     * @param type $data
     */
    public function onTask($serv, $task_id, $src_worker_id, $data) {
        $obj = new app\index\controller\Task();
        $name = $data['method'];
        $obj->$name($data['data'], $serv);
    }

    /**
     * 连接关闭
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactorId
     */
    function onClose($server, $fd) {
//        //关闭进程获取进程的key
        $chat = app\index\library\Redis::getInstance()->get(self::PROCESS . $fd);
        app\index\library\Redis::getInstance()->del(self::PROCESS . $fd);
        app\index\library\Redis::getInstance()->sRem($chat, $fd);
    }

}

new http();
