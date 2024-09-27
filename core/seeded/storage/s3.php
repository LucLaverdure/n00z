<?php


namespace MeshMVC\StorageTypes;

class S3 extends \MeshMVC\Storage {

    function connect(): \MeshMVC\Storage {
        // TODO: Implement connect() method.
    }

    function disconnect(): \MeshMVC\Storage {
        // TODO: Implement disconnect() method.
    }

    function upload($location, $data, $operation = "write"): \MeshMVC\Storage {
        // TODO: Implement upload() method.
    }

    function download($location): mixed {
        // TODO: Implement download() method.
    }

    function bulk_start(): \MeshMVC\Storage {
        // TODO: Implement bulk_start() method.
    }

    function query($query): mixed {
        // TODO: Implement query() method.
    }

    function bulk_end(): \MeshMVC\Storage {
        // TODO: Implement bulk_end() method.
    }

    function cache($key, $value): mixed {
        // TODO: cache in S3
    }

}
