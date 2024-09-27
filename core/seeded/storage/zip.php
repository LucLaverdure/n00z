<?php

namespace MeshMVC\StorageTypes;

class Zip extends \MeshMVC\Storage {

    public function __construct() {
        $args = func_get_args();
        return $this;
    }

    // required by abstract class
    public function connect() : self {
        $this->link(new ZipArchive());
        return $this;
    }

    // required by abstract class
    public function disconnect() : self {
        $this->link()->close();
        return $this;
    }

    // add file to compress
    public function upload($location, $data, $operation="") : self {
        $this->link()->addFromString($location, $data);
        return $this;
    }

    // get compressed file
    public function download($location) : mixed {
        $data = $this->link()->getFromName($location);
        return $this->disconnect();
    }

    public function query($query) : mixed {
        throw new \Exception("No query possible.");
    }

    function bulk_start(): \MeshMVC\Storage {
        // TODO: start compression
        $this->link(new ZipArchive());
        return $this;
    }

    function bulk_end(): \MeshMVC\Storage {
        // TODO: end compression
        return $this->disconnect();
    }

    function cache($key, $value): mixed {
        throw new \Exception("No caching possible.");
    }

}
