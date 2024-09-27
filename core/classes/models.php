<?php

namespace MeshMVC;

	// chainable class
	class Models {

        public static $models = [];

		public function __construct($name=null, $value = null) {
            if ($name != null) {
                if ($value == null) {
                    return $this->get($name);
                } else {
                    @$this->add($name, $value);
                }
            }
            return $this;
		}

        public function save($name, $filename) {
            self::$models[$name]->save($filename);
            return $this;
        }

        public function add($name, $value) {
            self::$models[$name] = $value;
            return $this;
        }

        public function remove($name) {
            unset(self::$models[$name]);
            return $this;
        }

        public static function getAll() {
            return self::$models;
        }

        public static function get($name) {
            return self::$models[$name];
        }

    }
