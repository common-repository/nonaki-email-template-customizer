<?php

namespace Nonaki;

defined('ABSPATH') || exit;

class Api extends Base
{
    use BaseTrait;

    public function init()
    {
        $this->route_sub_types();
        $this->route_media_library();
    }

    public function route_media_library()
    {
        add_action('rest_api_init', function () {
            register_rest_route('nonaki/v1', '/get/media', array(
                "methods" => \WP_REST_Server::READABLE,
                'callback' => [$this, 'media_library'],
                'permission_callback' => '__return_true',
            ));
        });
    }

    public function media_library()
    {
        return nonaki_get_media_library_images();
    }

    public function route_sub_types()
    {

        add_action('rest_api_init', function () {
            register_rest_route('nonaki/v1', '/get/subtypes', array(
                "methods" => \WP_REST_Server::READABLE,
                'callback' => [$this, 'sub_types_content'],
                'permission_callback' => '__return_true',
            ));
        });
    }

    public function sub_types_content()
    {
        return nonaki_get_template_sub_types('woddocommerce');
    }
}

(new Api)->init();
