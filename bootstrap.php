<?php
defined('ABSPATH') || exit;

class Nonaki_Bootstrap
{
    private static $instance;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private $required_files = [
        "trait", "base", "helper", "assets",
        "render", "api", "cpt", "metabox", "admin",
        "settings", "ajax", "shortcode", "menu", "presets",
    ];

    private $private_bootstrap_files = [
        "cpt", "metabox", "settings", "ajax",
    ];

    private $service_files = ['service/wp/init'];

    private $libs_file_names = ['modules/register-module'];

    private $public_bootstrap_files = ["shortcode"];

    private $modules = [
        "templates", 'contacts',
    ];

    public function __construct()
    {
        $this->add_supports()
            ->include_libs()
            ->import_core()
            ->import_modules()
            ->add_plugin_meta_links()
            ->add_services();
    }

    private function add_supports()
    {
        add_action('init', [$this, 'nonaki_woococommerce_support']);
        return $this;
    }

    private function add_plugin_meta_links()
    {
        if (is_admin()) {

            add_filter('plugin_action_links', function ($links, $file) {
                if ($file === 'nonaki/nonaki.php') {
                    $create_template_link = '<a href="' . admin_url('admin.php?page=nonaki-choice-type') . '" title="' . esc_html__('Create Template', 'nonaki') . '">' . esc_html__('Create Template', 'nonaki') . '</a>';
                    array_unshift($links, $create_template_link);
                }
                return $links;
            }, 10, 2);
        }

        return $this;
    }

    private function add_services()
    {
        foreach ($this->service_files as $sf) {
            require_once plugin_dir_path(__FILE__) . '/' . $sf . '.php';
        }

        return $this;
    }

    private function import_core()
    {
        foreach ($this->required_files as $cf) {
            require_once plugin_dir_path(__FILE__) . '//includes/' . $cf . '.php';
        }

        return $this;
    }

    private function import_modules()
    {
        foreach ($this->modules as $module) {
            require_once plugin_dir_path(__FILE__) . "//modules/" . $module . "/init.php";
            $module_class = "\Nonaki\Modules\\" . ucfirst($module);
            (new $module_class)->init();
        }

        return $this;
    }

    private function include_libs()
    {
        foreach ($this->libs_file_names as $file_name) {

            require_once "libs/{$file_name}.php";
        }
        return $this;
    }

    public function get_private_bootstrap_files()
    {
        return $this->private_bootstrap_files;
    }

    public function get_public_bootstrap_files()
    {
        return $this->public_bootstrap_files;
    }

    public function nonaki_woococommerce_support()
    {
        if (nonaki_woo_exists()) {
            remove_action('woocommerce_email_header', array(WC()->mailer(), 'email_header'));
            remove_action('woocommerce_email_footer', array(WC()->mailer(), 'email_footer'));
        }
    }
}

global $nonaki;
$nonaki = Nonaki_Bootstrap::get_instance();
