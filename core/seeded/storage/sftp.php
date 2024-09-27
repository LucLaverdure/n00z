<?php

namespace MeshMVC\StorageTypes;

class SFTP extends \MeshMVC\Storage {

    public function connect() : self {
        $args = func_get_args();

        // 1st argument: host
        $host = $args[0] ?? null;
        // 2nd argument: port
        $port = $args[1] ?? null;
        // 3rd argument: username
        $username = $args[2] ?? null;
        // 4th argument: password OR private key
        $password = $args[3] ?? null;
        // 5th argument: public key
        $public = $args[3] ?? null;
        // 5th argument: secret
        $secret = $args[4] ?? null;

        $this->link(ssh2_connect($host, $port));
        if (!$this->link()) {
            throw New \Exception('SFTP: Failed to connect to ' . $host . ' at port' . $port);
        }

        if (empty($secret)) {
            // connect by password
            if (!ssh2_auth_password($this->link(), $username, $password)) {
                throw New \Exception('SFTP: Failed to authenticate to ' . $host . ' at port' . $port. ' with user '.$username);
            }
        } else {
            // connect by keys
            if (!ssh2_auth_pubkey_file($this->link(), $username, $public, $password, $secret)) {
                throw New \Exception('SFTP: Failed to authenticate to ' . $host . ' at port' . $port. ' with user '.$username);
            }
        }

        return $this;
    }

    public function disconnect() : self {
        ssh2_disconnect($this->link());
        return $this;
    }

    public function upload($location, $data, $operation = "write") : self {
        try {
            $sftp = ssh2_sftp($this->link());
            $sftpStream = @fopen('ssh2.sftp://' . $sftp . $location, substr($operation, 0, 1));
            if (!$sftpStream) {
                throw new Exception("Could not open remote file: $location");
            }
            if (@fwrite($sftpStream, $data) === false) {
                throw new Exception("Could not send data.");
            }
        } catch (\Exception $e) {

        } finally {
            fclose($sftpStream);
        }
        return $this;
    }
    public function download($location) : mixed {
        $sftp = ssh2_sftp($this->link());
        $sftpStream = fopen("ssh2.sftp://$sftp/$location", 'r');
        if ($sftpStream === false) {
            throw new \Exception("Could not open remote file: $location");
        }
        $contents = stream_get_contents($sftpStream);
        fclose($sftpStream);
        return $contents;
    }

    public function query($query) : self {
        if (!ssh2_exec($this->link(), $query)) {
            throw new \Exception('Failed to execute query');
        }
        return $this;
    }

    function bulk_start(): \MeshMVC\Storage {
        throw new \Exception("No rollback possible.");
    }

    function bulk_end(): \MeshMVC\Storage {
        throw new \Exception("No rollback possible.");
    }

    function cache($key, $value): mixed {
        // TODO: cache in DB
    }

}
