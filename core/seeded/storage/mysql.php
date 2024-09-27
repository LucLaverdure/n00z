<?php

namespace MeshMVC\StorageTypes;

class MySQL extends \MeshMVC\Storage {

    public $last_statement = null;
    public $last_id = null;

    public function connect() : self {
        $args = func_get_args();

        // 1st argument: host
        $host = $args[0] ?? null;
        // 2nd argument: user
        $user = $args[1] ?? null;
        // 3rd argument: password
        $password = $args[2] ?? null;
        // 4th argument: database
        $database = $args[3] ?? null;
        // 5th argument: port
        $port = $args[4] ?? null;
        // 6th argument: socket
        $socket = $args[5] ?? null;

        $link = new \mysqli($host, $user, $password, $database, $port, $socket);
        if ($link === false) throw new \Exception("Error connecting to database.");

        $this->link($link);
        return $this;
    }

    public function disconnect() : self {
        // nothing to do.
        $this->link()->close();
        return $this;
    }

    // Bulk Insert with $table, $rows, $defs
    public function upload($location, $data, $operation = "write") : self {
        $table = $location;
        $rows = $data;
        $defs = $operation;

        $sql = "INSERT INTO ".$table." (";
        $cols = array();

        $types = "";
        foreach($defs as $def) {
            foreach($def as $col => $type) {
                $cols[] = $col;
                $types .= $type;
            }
        }
        $sql .= implode(",", $cols);
        $sql .= ") VALUES (";

        $params = array();
        foreach ($rows as $r) {
            $params[] = "?";
        }
        $sql .= implode(",", $params);

        $sql .= ");";

        $this->query($sql, $types, ...$rows);
        return $this;
    }

    public function query($query) : array {
        // init vars
        $args = func_get_args();
        $query = array_shift($args);
        $stmt =  $this->link()->stmt_init();

        // ex: INSERT INTO CountryLanguage VALUES (?, ?);
        if ($stmt->prepare($query)) {
            if (count($args) > 0) {
                $params_config = array_shift($args);

                // secure parameters into SQL query statement with unpacking parameters(...)
                $stmt->bind_param($params_config, ...$args);
            }
        }

        // execute SQL query
        $x = $stmt->execute();

        if (!$x) $this->bulk_failed(true);

        $ret = [];
        $results = $stmt->get_result();
        while (($results != null) && ($row = $results->fetch_array(MYSQLI_BOTH))) {
            $ret[] = $row;
        }
        return $ret;
    }

    public function getLastStatement() : mixed {
        return $this->last_statement;
    }

    public function getLastInsertId() : mixed {
        return $this->link()->insert_id;
    }

    public function download($location) : mixed {
        return $this->query($location);
    }

    function bulk_start(): self {
        $this->link()->autocommit(false);
        return $this;
    }

    function bulk_end(): self {
        if ($this->bulk_failed()) {
            // rollback
            $this->link()->rollback();
        } else {
            // commit
            $this->link()->commit();
        }
        $this->link()->autocommit(true);
        $this->bulk_failed(false);
        return $this;
    }

    function cache($key, $value): mixed {
        // TODO: cache in DB
    }

}
