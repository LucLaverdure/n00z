<?php

namespace MeshMVC\Views;
use MeshMVC\View;

// Core Controller class for all controller objects to extend
class Json extends View {

    public function parse($previousOutput = "") : string {
        $from = $this->from;
        $filter = $this->filter; // TODO: filter
        $trim = $this->trim;
        $to = $this->to;
        $display_mode = $this->display_mode;
        $destination = "";
        $processed_output = "";
        $place_me = null;

        // no view template specified
        if ($from == "") throw new \Exception("No view template specified!");

        if (is_string($from) && (str_starts_with($from, "/") || \MeshMVC\Tools::is_url($from))) {
            if (empty($this->storage)) throw new \Exception("No storage set on this view.");
            $processed_output = $this->storage->download($from);
        } elseif (is_object($from) && in_array('MeshMVC\Model', class_parents($from), true)) {
            $processed_output = $from->json();
        } else {
            $processed_output = $from;
        }

        $place_me = $processed_output;

        if (empty($to)) {
            // override all previous templates if no target specified
            return $place_me;
        }

        // when target specified, place new json
        $destination = $previousOutput;

        if (!empty($to) && !empty($place_me)) {
            // prepend, append, replace, merge (default)
            switch ($display_mode) {
                case "prepend":
                    $destination = \MeshMVC\Tools::jsonPrepend($destination, $to, $place_me);
                    break;
                case "append":
                    $destination = \MeshMVC\Tools::jsonAppend($destination, $to, $place_me);
                    break;
                case "merge":
                    $destination = \MeshMVC\Tools::jsonMerge($destination, $to, $place_me);
                    break;
                default: // replace
                    $destination = \MeshMVC\Tools::jsonReplace($destination, $to, $place_me);
            }
        } elseif (!empty($to)) {
            $destination = $place_me;
        }

        if (!empty($trim)) {
            $destination = \MeshMVC\Tools::jsonRemoveMatching($destination, $trim);
        }

        if ($destination == null) $destination = "";

        return $destination;

    }
}