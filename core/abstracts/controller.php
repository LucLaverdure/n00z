<?php

namespace MeshMVC;

/**
 * Core Controller class for all controller objects to extend
 * @link https://meshmvc.com/????
 */
abstract class Controller {

    // unit tests results
    private $unit_tests = [];

    public $loaded_views = [];

    public $needed_controllers = [];

    public $index = null;

    public function __construct() {
        \MeshMVC\Cross::$currentController = $this;
    }

    public function __destruct() {
    }


    /**
     * Controller signature: Determines if current controller should run
     * @link https://meshmvc.com/????
     * @return mixed when false, controller doesn't run.
     * when a [number], controller will run with [number] as priority to sort all controllers (from lowest to highest)
     * when anything else, controller will run with normal priority.
     */
    abstract public function sign();

    /**
     * Controller's main backend code to be executed. Typically used to render views as HTML or APIs.
     * @link https://meshmvc.com/????
     */
    abstract public function run();

    public function addView($view) {
        \MeshMVC\Cross::$currentController = $this;
        $this->loaded_views[] = $view;
    }

    public function passed($log) {
        \MeshMVC\Cross::$currentController = $this;
        // log success test
        $this->note($log);
        $this->unit_tests[] = true;
    }

    public function failed($log) {
        \MeshMVC\Cross::$currentController = $this;
        // log failed test
        $this->note($log);
        $this->unit_tests[] = false;
    }

    public function unit_tests_passed() {
        \MeshMVC\Cross::$currentController = $this;
        if (count($this->unit_tests) > 0) {
            if (in_array(false, $this->unit_tests)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Attaches one or more controllers to be loaded before this one as a dependency
     * @link https://meshmvc.com/????
     * @param mixed $controller_list <p>
     * The controller name(s)
     * </p>
     * <p>
     * If controller_list is a string, a single controller is added as a dependency..
     * </p>
     * <p>
     *  If controller_list is an array of strings, each controller is added as a dependency..
     *  </p>         *
     * @return bool always returns true. (boolean safe)
     */
    public function needs($controller_list) {
        \MeshMVC\Cross::$currentController = $this;
        $controllers = is_array($controller_list) ? $controller_list : explode(",", $controller_list);
        $this->needed_controllers = array_merge($this->needed_controllers, array_map('trim', $controllers));
        return true;
    }

    // MVC shortcuts
    public function n($controller_list) {
        return $this->needs($controller_list);
    }
    public function r($controller_list) {
        return $this->needs($controller_list);
    }
    public function req($controller_list) {
        return $this->needs($controller_list);
    }
    public function requires($controller_list) {
        return $this->needs($controller_list);
    }

    // Misc Shortcuts
    public function q($arg) {
        \MeshMVC\Tools::queryURL($arg);
    }
    public function route($arg) {
        \MeshMVC\Tools::queryURL($arg);
    }
}
