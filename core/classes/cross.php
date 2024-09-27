<?php

/**
 * Class Cross
 *
 * The Cross class is used for global shortcuts to access current controllers, views, models, loggers, and storage types.
 */

namespace MeshMVC;
use /**
 * The Views namespace is responsible for handling the presentation layer of the application.
 * It provides classes for rendering and displaying views or templates to the user.
 *
 * @package MeshMVC\Views
 */
    \MeshMVC\Views;
use /**
 * This class represents the available storage types in MeshMVC framework.
 */
    \MeshMVC\StorageTypes;

    // used for global shortcuts to access current controllers and views

/**
 * Class Cross
 *
 * The Cross class is responsible for managing the current controller, current view, models, loggers and storage instances.
 */
class Cross {
	    public static $currentController = null;
	    public static $currentView = null;
        public static $models = null;
        public static $loggers = [];
        public static $storage = [];

        // TODO: automate these values
        public static $viewTypes = [
            "html" => Views\Html::class,
            "text" => Views\Text::class,
        ];

        // TODO: automate these values
        public static $storageTypes = [
            "curl" => StorageTypes\Curl::class,
            "local" => StorageTypes\Local::class,
            "mysql" => StorageTypes\MySQL::class,
            "s3" => StorageTypes\S3::class,
            "sftp" => StorageTypes\SFTP::class,
            "zip" => StorageTypes\Zip::class,
        ];

        public static $loggerTypes = [
            "logger" => \MeshMVC\Loggers\Logger::class,
        ];

        public function __construct() {
            self::$models = new \MeshMVC\Models();
        }

        public static function storage($alias, $instance = null) {
            if (empty($instance)) return self::$storage[$alias];
            self::$storage[$alias] = new \MeshMVC\Mesh($instance);
            return self::$storage[$alias];
        }
        public static function storages() : array {
            return self::$storage;
        }

        public static function logger($alias, $instance = null) {
            if (empty($instance)) return self::$loggers[$alias];
            self::$loggers[$alias] = new \MeshMVC\Mesh($instance);
            return self::$loggers[$alias];
        }
        public static function loggers() : array {
            return self::$loggers;
        }
    }