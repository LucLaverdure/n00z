<?php

namespace MeshMVC;

	// chainable class
	class User  {

	    public $lang = "EN";

		public function __construct () {
		    // load settings from session
			return $this;
		}

		public function auth() {
			return $this;
		}

		public function load() {
			return $this;
		}
	}
