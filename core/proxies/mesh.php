<?php

namespace MeshMVC;

class Mesh {

    private $class = null;
    private $performance = [];

    public function __construct($class) {
        $this->class = $class;
        return $this->class;
    }

    public function performance() {
        return $this->performance;
    }

    public function performance_pop() {
        return end($this->performance)["timed"];
    }

    public function __call($name, $args) {

        if ($_ENV["config"]["debug"]) $outer_start = microtime(true);

        if (property_exists($this->class, "__before__call")) $this->class->__before__call();
        $before_name = "__before__$name";
        if (property_exists($this->class, $before_name)) $this->class->$before_name();

        if ($_ENV["config"]["debug"]) $start = microtime(true);

        $ret = $this->class->$name(...$args);

        if ($_ENV["config"]["debug"]) $end = microtime(true);

        $after_name = "__after__$name";
        if (property_exists($this->class, $after_name)) $this->class->$after_name();
        if (property_exists($this->class, "__after__call")) $this->class->__after__call();

        if ($_ENV["config"]["debug"]) $outer_end = microtime(true);

        if ($_ENV["config"]["debug"]) $this->performance[] = ["function"=>$name, "start"=>$start, "end"=>$end, "timed" => $end-$start, "outer_timed"=>$outer_end-$outer_start ];

        return $ret;
    }

    public function __get($name) {
        $before_name = "__before__$name";
        if (property_exists($this->class, $before_name)) $this->class->$before_name();
        $ret = $this->class->$name;
        $after_name = "__after__$name";
        if (property_exists($this->class, $after_name)) $this->class->$after_name();
        return $ret;
    }

    public function __set($name, $value) : void {
        $before_name = "__before__$name";
        if (property_exists($this->class, $before_name)) $this->class->$before_name();
        $this->class->$name = $value;
        $after_name = "__after__$name";
        if (property_exists($this->class, $after_name)) $this->class->$after_name();
    }

}
