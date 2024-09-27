<?php

    $_ENV["performance_start"] = microtime(true);

    // display core errors!
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('session.cookie_lifetime', 1 * 24 * 60 * 60);
    ini_set('session.gc_maxlifetime', 1 * 24 * 60 * 60);


// init session
	session_start();

	// randomize seed
	srand();

	// set main path for files fetching
    $_ENV["PATH"] = realpath($_SERVER["DOCUMENT_ROOT"]."/../")."/";

	// Includes
	require_once $_ENV["PATH"]."core/classes/tools.php";
    require_once $_ENV["PATH"]."core/classes/config.php";

	// Configs
	require_once $_ENV["PATH"]."core/classes/load-configs.php";

    // shortcuts
    require_once $_ENV["PATH"]."core/classes/shortcuts.php";

	// routes (f=*, !q=*)
	require_once $_ENV["PATH"]."core/classes/route.php";
	
	// Class containers
    require_once $_ENV["PATH"]."core/classes/models.php";
	require_once $_ENV["PATH"]."core/classes/cross.php"; @new \MeshMvc\Cross(); // TODO: move into queue

    // proxies (mesh)
    require_once $_ENV["PATH"]."core/proxies/mesh.php";

    // Abstracts
    foreach (glob($_ENV["PATH"] . "core/abstracts/*.php") as $filename) {
        require_once $filename;
    }

    // Abstract Extensions
    foreach (glob($_ENV["PATH"] . "core/seeded/*/*.php") as $filename) {
        require_once $filename;
    }

	// Execution queue start
	require_once $_ENV["PATH"]."core/classes/queue.php";
