<?php 
namespace Nonaki\Services\WP;

defined('ABSPATH') || exit;

trait  Base {
    public static $instance;
    public $type = 'wordpress';
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}