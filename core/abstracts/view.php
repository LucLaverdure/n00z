<?php

namespace MeshMVC;

// Core Controller class for all controller objects to extend
	abstract class View {

        public $storage = null;

		public $from = ''; 	// from template
        public $filter = '';
        public $trim = '';
        public $to = ''; 	// to selector -> default override all previous templates
        public $cache = false; // caching
        public $vars = [];

        public $display_type = "html"; // html, text or json
        public $display_mode = "append"; // or prepend, append, replace, replace_inner

        public $use_models = true; // default: true (render models within brackets ex: "[user.email]")
        public $doRenderOnDestruct = true;

        // constructor requires filename of view
        public function __construct() {
            \MeshMVC\Cross::$currentView = $this;
            $this->storage($_ENV["config"]["default_storage"]);
			return $this;
        }

        public abstract function parse($previousOutput = ""): string;

        //return as string
        public function toString() {
            \MeshMVC\Cross::$currentView = $this;
            $this->doRenderOnDestruct = false;
            return $this->parse();
        }

		// set caching
		public function cache($setCaching) {
            \MeshMVC\Cross::$currentView = $this;
            $this->cache = $setCaching;
            return $this;
		}

		// send view as email
        // TODO: send email
		public function email($subject, $email) {
            \MeshMVC\Cross::$currentView = $this;
            return $this;
		}

		// write to file
		public function export($filename) {
            \MeshMVC\Cross::$currentView = $this;
            $this->storage->upload($filename, $this->parse());
            return $this;
		}

		// from template filename
		public function from($from) {
            \MeshMVC\Cross::$currentView = $this;
            $this->from = $from;
			return $this;
		}

        public function vars($variables) {
            \MeshMVC\Cross::$currentView = $this;
            $this->vars = $variables;
            return $this;
        }

        // filter applied on from
		public function filter($filter) {
            \MeshMVC\Cross::$currentView = $this;
			$this->filter = $filter;
			return $this;
		}

        public function trim($filter) {
            \MeshMVC\Cross::$currentView = $this;
            $this->trim = $filter;
            return $this;
        }

		// "to" selector
 		public function to($to) {
            \MeshMVC\Cross::$currentView = $this;
			$this->to = $to;
			return $this;
		}
		// force display type (html or json) / this is usually done automatically by analysing the ->from property
		public function display_type($display_type) {
            \MeshMVC\Cross::$currentView = $this;
			$this->display_type = $display_type;
			return $this;
		}
		// HTML: inner, prepend, replace, append (default)
        // JSON: prepend, append, replace, merge (default)
		public function by($merge_mode) {
            \MeshMVC\Cross::$currentView = $this;
			$this->display_mode = $merge_mode;
			return $this;
		}
		// use models in templates
		public function use_models($use_models) {
            \MeshMVC\Cross::$currentView = $this;
			$this->use_models = $use_models;
			return $this;
		}

        public function storage($alias) {
            \MeshMVC\Cross::$currentView = $this;
            if (empty($alias)) return $this->storage;
            $this->storage = storage($alias);
            return $this;
        }

	}
