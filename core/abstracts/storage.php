<?php

namespace MeshMVC;

abstract class Storage {

    private $performance = null;
    private mixed $link = null;
    private mixed $bulk_failed = false;
    private $options = [];

    abstract function connect() : self;
    abstract function disconnect() : self;

    // gets or sets cache
    abstract function cache($key, $value) : mixed;

    abstract function upload($location, $data, $operation = "write") : self; // on fail: throw error
    abstract function download($location) : mixed;

    abstract function bulk_start() : self;
    abstract function query($query) : mixed;
    abstract function bulk_end() : self;

    public function __contruct() {
        $args = func_get_args();
        return $this->connect(...$args);
    }

    public function link($link = null) : mixed {
        if (empty($link)) return $this->link;
        $this->link = $link;
        return $this;
    }

    public function bulk_failed($status = null) : mixed {
        if (empty($status)) return $this->bulk_failed;
        $this->bulk_failed = $status;
        return $this;
    }

    public function option($name, $value=null) : mixed {
        if (empty($value)) return $this->options[$name];
        $this->options[$name] = $value;
        return $this;
    }

    public function options() : array {
        return $this->options;
    }

    public function opt($name, $value=null) : mixed {
        return $this->option($name, $value);
    }

    public function export($input_alias, $input_location, $output_alias, $output_location) {
        $this->performance_start();
        storage($output_alias)->upload($output_location, storage($input_alias)->download($input_location));
        $this->performance_end();
    }

    public static function prefix_download(&$url) {
        if (str_starts_with($url, "/")) {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            $baseUrl = $protocol . $domainName;
            $url = $baseUrl.$url;
        }
    }

}
