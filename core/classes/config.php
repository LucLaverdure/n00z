<?php

namespace MeshMVC;

class CoreConfig {
    public static function storage($alias, $id = null, ...$storageConfig) {
        return \MeshMVC\Cross::storage($alias, $id, ...$storageConfig);
    }
}
