<?php

// TODO: put this file into objects or something....

$new_path = $_ENV["PATH"];
set_include_path(get_include_path() . PATH_SEPARATOR . $new_path);
$new_path = $_ENV["PATH"]."html";
set_include_path(get_include_path() . PATH_SEPARATOR . $new_path);

require_once $_ENV["PATH"]."core/lib/autoload.php";
use Symfony\Component\Yaml\Yaml;

$yamlFile = $_ENV["PATH"].'compose.yaml';
$yaml = Yaml::parseFile($yamlFile);
$_ENV["config"] = $yaml["x-meshmvc"];

// Set error display settings based on debug mode
if ($_ENV["config"]["debug"]) {
    // Debug mode: display errors
    ini_set("html_errors", "1");
    ini_set("error_prepend_string", "<pre style='color: #333; font-family: source-code-pro, Menlo, Monaco, Consolas, 'Courier New', monospace; font-size:14px;'>");
    ini_set("error_append_string", "</pre>");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Non-debug mode: suppress errors and warnings
    error_reporting(0);
    ini_set('display_errors', 0);
}

