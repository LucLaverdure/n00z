<?php

namespace MeshMVC\StorageTypes;

class Curl extends \MeshMVC\Storage {

    private string $agentString = "MeshMVC/1.0 (compatible; MSIE 5.01; Windows NT 5.0)";
    private int $timeoutMS = 60 * 1000;
    private $proxy = null;

    public function __construct() {
        return $this;
    }

    // required by abstract class
    public function connect() : self {
        $this->link(curl_init());
        return $this;
    }

    // required by abstract class
    public function disconnect() : self {
        // do nothing.
        return $this;
    }

    public function upload($location, $data, $operation="") : self {
        throw new \Exception("curl can only download.");
    }

    public function agent($agentString) : self {
        $this->agentString = $agentString;
        return $this;
    }

    public function timeout($miliseconds) : self {
        $this->timeoutMS = $miliseconds;
        return $this;
    }

    public function proxy($miliseconds) : self {
        $this->timeoutMS = $miliseconds;
        return $this;
    }

    public function download($location) : mixed {
        $this->prefix_download($location);

        curl_setopt($this->link(), CURLOPT_URL, $location);
        curl_setopt($this->link(), CURLOPT_PROXY, $this->proxy);
        curl_setopt($this->link(), CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->link(), CURLOPT_USERAGENT, $this->agentString);
        curl_setopt($this->link(), CURLOPT_AUTOREFERER, true);
        curl_setopt($this->link(), CURLOPT_FOLLOWLOCATION, true);

        foreach ($this->options() as $option => $value) {
            curl_setopt($this->link(), $option, $value);
        }

        if ($this->timeoutMS != null) {
            curl_setopt($this->link(), CURLOPT_CONNECTTIMEOUT_MS, $this->timeoutMS);
        }

        $output = curl_exec($this->link());

        // get proxy errors
        if (curl_getinfo($this->link(), CURLINFO_PROXY_ERROR) != CURLPX_OK) {
            throw new \Exception("Proxy(".CURLOPT_PROXY.") error downloading: ".$url);
        }

        // get response code to ensure
        if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
            $code = (int)curl_getinfo($this->link(), CURLINFO_RESPONSE_CODE);
            if ($code != 200) throw new \Exception($code . " error downloading: " . $location);
        }

        curl_close($this->link());
        return $output;
    }

    public function query($query) : mixed {
        throw new \Exception("curl can only download.");
    }

    function bulk_start(): \MeshMVC\Storage {
        throw new \Exception("No rollback possible.");
    }

    function bulk_end(): \MeshMVC\Storage {
        throw new \Exception("No rollback possible.");
    }

    function cache($key, $value): mixed {
        throw new \Exception("No caching possible.");
    }
}
