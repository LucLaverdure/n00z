<?php


namespace MeshMVC\Loggers;

abstract class Logger extends \MeshMVC\Logger {

    public function log($message, $throw = false, $level = 0) : self {
        $message = date("Y-m-d H:i:s")." $message";
        $this->storage->upload($_ENV["config"]["error_logs"], "$message \n", "append");
        return $this;
    }

}
