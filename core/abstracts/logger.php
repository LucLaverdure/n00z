<?php

namespace MeshMVC;

abstract class Logger {

    public $storage = null;

    abstract public function log($message, $throw=false, $level=0);

    public function __construct($storage=null) {
        if (empty($storage)) {
            // use default storage
            $this->storage = storage();
        } else {
            // use custom storage
            // TODO: instance and mesh it.
            $this->storage = $storage;
        }

    }
}
