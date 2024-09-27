<?php
	namespace MeshMVC;

    // include core libs
	require_once($_ENV["PATH"] . 'core/lib/phpquery/phpQuery-onefile.php');

	class Queue {
		public static $complete_output = "";

		private $obj_controllers = []; // Controllers Found
		private static $resources = Array(); // All stacked data
		
        public function __construct() {
            // Get the count of PHP's core declared classes
            $system_classes_count = count(get_declared_classes());

            // Initialize an array to store paths to load
            $paths_to_load = [];

            // Search for all files within the seeded directories
            foreach ($_ENV["config"]["seeds"] as $dir) {
                [$seed_type, $seeded_path] = explode(":", $dir);
                if ($seed_type === "controller" || $seed_type === "view") {
                    $paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($seeded_path));
                }
            }
            // Remove duplicates from the paths to load
            $paths_to_load = array_unique($paths_to_load);

            // Include each path
            foreach ($paths_to_load as $path) {
                require_once $path;
            }

            // Get the custom classes by slicing the declared classes array
            $custom_classes = array_slice(get_declared_classes(), $system_classes_count);

            // Instantiate each controller found (identified by class extended from Controller)
            foreach ($custom_classes as $class) {
                if (in_array('MeshMVC\Controller', class_parents($class), true)) {
                    $this->obj_controllers[$class] = new \MeshMVC\Mesh(new $class);
                }
                if (in_array('MeshMVC\View', class_parents($class), true)) {
                    Cross::$viewTypes[strtolower($class)] = $class::class;
                }
            }
        }

        // TODO: move this function to a core module
        public static function output()  {
            $paths_to_load = [];

            $doc = \phpQuery::newDocumentHTML(self::$complete_output);
            $headed = $doc["html head"]->length;

            if ($headed) {
                foreach ($_ENV["config"]["seeds"] as $dir) {
                    [$seed_type, $seeded_path] = explode(":", $dir);

                    if ($seed_type === "css" || $seed_type === "js") {
                        $paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($seeded_path));
                    }
                }

                $paths_to_load = array_unique($paths_to_load);

                foreach ($paths_to_load as $resource) {
                    if (file_exists($resource)) {
                        $ext = pathinfo($resource, PATHINFO_EXTENSION);
                        $append_header = '';

                        if ($ext === "css") {
                            $append_header = '<link href="/' . $resource . '" rel="stylesheet" />';
                        } elseif ($ext === "js") {
                            $append_header = '<script src="/' . $resource . '" type="text/javascript"></script>';
                        }

                        $doc["html head"]->append($append_header);
                    }
                }

                self::$complete_output = $doc;
            }

            return self::$complete_output;
        }


        /* Main process thread */
        public function process()
        {
            // validate and assign priorities to controllers
            $priority_controllers = [];

            // ensure all unit tests pass when debugging
            if ($_ENV["config"]["debug"]) {
                $unit_tests_failed = false;
                foreach ($this->obj_controllers as $cname => $controller) {
                    if (method_exists($controller, 'test')) {
                        \MeshMVC\Cross::$currentController = $controller;
                        $do_unit_test = $controller->test();
                        if (!$controller->unit_tests_passed()) {
                            // controller unit tests failed
                            $unit_tests_failed = true;
                        } else {
                            // controller unit tests passed
                        }
                    } else {
                        // controller has no unit tests
                    }
                }

                // logging happens in controllers
                if ($unit_tests_failed) {
                    echo "UNIT TEST(S) FAILED!";
                    die();
                }
            }

            // ensure controllers validate when a signature function is found
            foreach ($this->obj_controllers as $cname => $controller) {
                \MeshMVC\Cross::$currentController = $controller;
                $validator_priority = $controller->sign();
                if ($validator_priority !== false) {
                    // when controller validates
                    $validator_priority = is_numeric($validator_priority) ? $validator_priority : 0;

                    $priority_controllers[$cname] = $validator_priority;
                } else {
                    // free resources and invalidate obj
                    $this->obj_controllers[$cname] = null;
                }
            }

            //debug($priority_controllers);
            // sort controllers by highest priority to lowest
            uasort($priority_controllers, 'MeshMVC\Tools::prioritySorter');
            //debug($priority_controllers);

            // prevent infinite loops & dependencies to controllers that don't exist
            $invalid_controllers = [];
            foreach ($priority_controllers as $this_controller_name => $priority_c) {
                \MeshMVC\Tools::dig_dependencies($this_controller_name, [], $this->obj_controllers, $invalid_controllers);
            }

            // remove invalid controllers
            foreach ($invalid_controllers as $this_controller_name => $error_message) {
                unset($priority_controllers[$this_controller_name]);
            }

            // output invalid controller in debug mode
            // debug($invalid_controllers);

            // do topological sort
            $sorter = new \MJS\TopSort\Implementations\StringSort();
            foreach ($priority_controllers as $this_controller_name => $priority_c) {
                $sorter->add($this_controller_name, $this->obj_controllers[$this_controller_name]->needed_controllers);
            }
            $controllers_sorted = $sorter->sort();

            // index of controllers
            $controller_index = 0;

            // set complete output
            self::$complete_output = "";

            foreach ($controllers_sorted as $i => $cname) {
                $cname = trim($cname);
                if ($cname !== "") {
                    // find controller of class name
                    $controller = $this->obj_controllers[$cname];
                    $controller->index = $controller_index++;

                    \MeshMVC\Cross::$currentController = $controller;
                    @$controller->run();

                    // render each queued views
                    foreach ($controller->loaded_views as $view) {
                        if ($view->doRenderOnDestruct) {
                            self::$complete_output = $view->parse(self::$complete_output);
                        }
                    }
                }
            }

            echo self::output();

            // disconnect all storages
            foreach (\MeshMVC\Cross::storages() as $id => $storage) {
                try {
                    $storage->disconnect();
                } catch(\Exception $e) {
                    if ($_ENV["config"]["debug"]) {
                        debug("Failed to disconnect storage[" . $id . "]");
                    }
                }
            }

            // TODO: load path from environment config
            $access_log = (substr($_ENV['config']['logs']['access_logs'], 0, 1) === '/') ? $_ENV['config']['logs']['access_logs'] : $_ENV['PATH'] . $_ENV['config']['logs']['access_logs'];
            $time = microtime(true) - $_ENV["performance_start"];
            $remote_ip = $_SERVER['REMOTE_ADDR'].(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? "(" . $_SERVER['HTTP_X_FORWARDED_FOR'] . ")" : "");
            storage()->upload($access_log, "200 (".method()."): ".\MeshMVC\Tools::queryURL().", $time, $remote_ip \n", "append");
        }
	
	}

	// Run Core
	$core = new \MeshMVC\Queue();
	$core->process();
