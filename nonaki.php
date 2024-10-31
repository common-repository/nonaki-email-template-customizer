<?php

/**
 * Plugin Name: Nonaki Email Template Builder
 * Description: Drag and Drop email template builder for wodpress
 * Plugin URI:  https://wpcox.com/nonaki
 * Version:     1.0.11
 * Author:      Prappo
 * Author URI:  https://wpcox.com/nonaki
 * Text Domain: nonaki
 */

defined('ABSPATH') || exit;

use Nonaki\BaseTrait;

require_once plugin_dir_path(__FILE__) . '/bootstrap.php';

final class Nonaki_Email
{
    use BaseTrait;

    public function __construct()
    {
        define('NONAKI_VERSION',             '1.0.11');
        define('NONAKI_DIR',                 plugin_dir_path(__FILE__));
        define('NONAKI_URL',                 plugin_dir_url(__FILE__));
        define('NONAKI_ASSETS_URL',          NONAKI_URL . '/assets');
        define('NONAKI_ADMIN_DASHBOARD_URL', admin_url('edit.php?post_type=nonaki_email_template'));
        define('NONAKI_EDITOR_URL',          admin_url('index.php?page=nonaki'));
    }

    /**
     * ---------------------------------
     *  Main execution point where the 
     *       plugin will fire up
     * ---------------------------------
     * 
     * @since 1.0.0
     * @return void
     */
    public function init()
    {
        if (is_admin()) {
            $this->private_bootstrap();
            $this->add_menu();
            $this->nonaki_editor();
        }
        $this->public_bootstrap();
        add_action('init', array($this, 'i18n'));
    }

    private function add_menu()
    {
        add_action('admin_menu', [\Nonaki\Menu::get_instance(), 'init']);
    }

    /*
     * -----------------------------------------------
     * Loading and initializing core files that is
     * private and for internal use
     * -----------------------------------------------
     * 
     * @since 1.0.0
     * @return void
     */
    private function private_bootstrap()
    {
        global $nonaki;
        foreach ($nonaki->get_private_bootstrap_files() as $core_file) {
            $class_name = "\Nonaki\\" . ucfirst($core_file);
            $class_name::get_instance()->init();
        }
    }

    private function public_bootstrap()
    {
        global $nonaki;
        foreach ($nonaki->get_public_bootstrap_files() as $core_file) {
            $class_name = "\Nonaki\\" . ucfirst($core_file);
            $class_name::get_instance()->init();
        }
    }

    public function nonaki_editor()
    {
        $get_data  = wp_unslash($_GET);
        if (empty($get_data['page']) || 'nonaki' !== $get_data['page']) {
            return;
        }

        ob_start();
        $this->nonaki_editor_template();
        exit;
    }

    public function nonaki_editor_template()
    {
        require NONAKI_DIR . 'editor/init.php';
    }

    public function i18n()
    {
        load_plugin_textdomain('nonaki', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
}

function nonaki_email_template_builder_init()
{
    Nonaki_Email::get_instance()->init();
}

add_action('plugins_loaded', 'nonaki_email_template_builder_init');
