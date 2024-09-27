<?php

namespace MeshMVC;

// This file routes "/find" URLs from seeds to full paths

// When no URL is provided and a file is provided, look up the file in the seeded stack
if (!empty($_REQUEST['f']) && empty($_REQUEST['q'])) {
    $path_to_file = $_REQUEST['f'];
    if (strpos($path_to_file, '..') !== false || strpos($path_to_file, 'private') !== false) {
        die();
    }

    // Search for all files within the seeded directories
    $paths_to_load = [];
    $initial_dirs = explode(",", SEEDS);
    foreach ($initial_dirs as $dir) {
        $dir = explode(":", $dir);
        $seed_type = $dir[0];
        $seeded_path = $dir[1];
        $paths_to_load = array_merge($paths_to_load, Tools::search_files($seeded_path));
        foreach ($paths_to_load as $key => $tpl) {
            if (basename($_REQUEST['f']) != basename($tpl) && $seed_type == "media") {
                unset($paths_to_load[$key]);
            }
        }
    }
    $paths_to_load = array_unique($paths_to_load);

    if (!empty($paths_to_load)) {
        foreach ($paths_to_load as $path) {
            if (@file_exists($path)) {
                $arr = explode('.', $path);
                $ext = end($arr);
                switch ($ext) {
                    case "css":
                        header("Content-Type: text/css");
                        break;
                    case "js":
                        header("Content-Type: text/javascript");
                        break;
                    default:
                        header("Content-Type: " . mime_content_type($path));
                        break;
                }
                echo @file_get_contents($path, false);
                die();
            }
        }
    } else {
        die();
    }
}