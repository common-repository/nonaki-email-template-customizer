<?php

namespace Nonaki;

defined('ABSPATH') || exit;

class Assets extends Base
{
    use BaseTrait;
    public function init()
    {
        add_action('current_screen', [$this, 'add_assets']);
    }

    public function add_assets()
    {
        $current_screen = get_current_screen();
        error_log($current_screen->id);
        if ($current_screen->id === 'toplevel_page_wpnonaki') {
            $this->add_dashboard_scripts();
        }


        if ($current_screen->id === 'nonaki_page_nonaki-choice-type' || $current_screen->id === 'nonaki_page_nonaki-addons') {
            $this->add_new_template_scripts();
        }
    }

    public function add_dashboard_scripts()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_register_style('nonaki_dashboar_style', NONAKI_ASSETS_URL . '/parts/css/dashboard.css', false, NONAKI_VERSION);
            wp_enqueue_style('nonaki_dashboar_style');
        });
    }

    public function add_new_template_scripts()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_register_style('nonaki_add_new_template_style', NONAKI_ASSETS_URL . '/parts/css/new-template.css', false, NONAKI_VERSION);
            wp_enqueue_style('nonaki_add_new_template_style');
        });
    }
}

(new Assets)->init();
