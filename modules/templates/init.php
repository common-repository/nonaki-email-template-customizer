<?php

/**
 * ---------------------------------------------------
 * 
 * REST API that will provide presets functionality
 * To the editor. Editor will fetch data from 
 * yoursite.com/wp-json/nonaki/v1/presets
 * 
 * ---------------------------------------------------
 */

namespace Nonaki\Modules;

use Nonaki\BaseTrait;

class Templates
{
    use BaseTrait;

    public function init()
    {
        add_action("rest_api_init", [$this, "template_rest_api"]);
        require_once "email-presets/general/general.php";
        require_once "email-presets/general1/general.php";
        require_once "email-presets/general2/general.php";
        require_once "email-presets/general3/general.php";
        require_once "email-presets/password1/general.php";
        require_once "email-presets/password2/general.php";
    }

    public function template_rest_api()
    {
        register_rest_route('nonaki/v1', 'presets/email', [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, 'presets_contents'],
            'permission_callback' => '__return_true',
        ]);
    }


    public function presets_contents()
    {
        $data = [];
        $presets = apply_filters('nonaki_email_presets', $data);
        return $presets;
    }

    public static function get_presets_url()
    {
        return get_site_url(null, '/wp-json/nonaki/v1/presets/email');
    }
}
