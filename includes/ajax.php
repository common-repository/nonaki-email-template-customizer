<?php

namespace Nonaki;

class Ajax extends Base
{
    use BaseTrait;

    public function init()
    {
        add_action('wp_ajax_nk_save_post',     [$this, 'save_content']);
        add_action('wp_ajax_nk_send_mail',     [$this, 'send_mail']);
        add_action('wp_ajax_nk_get_sub_types', [$this, 'get_sub_types']);
        add_filter('kses_allowed_protocols',   [$this, 'allowed_protocols']);
    }

    public function allowed_protocols($protocols)
    {
        $protocols[] = 'data';
        return $protocols;
    }

    public function save_content()
    {
        nonaki_save_editor_content($_POST);
        wp_die();
    }

    public function send_mail()
    {
        nonaki_send_email(
            sanitize_text_field($_POST['to']),
            sanitize_text_field($_POST['from']),
            sanitize_text_field($_POST['subject']),
            sanitize_text_field($_POST['template_id']),
            sanitize_text_field($_POST['provider']),
            sanitize_text_field($_POST['type']),
        );
        wp_die();
    }

    public function get_sub_types()
    {
        if (isset($_POST['sub_type'])) {

            $template_id = sanitize_text_field($_POST['template_id']);
            $sub_types_data = nonaki_get_template_sub_types(sanitize_text_field($_POST['sub_type']));
            $is_new = sanitize_text_field($_POST['is_new']);
            // if ($is_new) {

            //     if ($is_new != 'none') {
            //         echo nonaki_e(Render::select('nonaki_sub_type', $is_new, ($sub_types_data)));
            //     }
            // }

            $selected = get_post_meta($template_id, 'template_sub_type', true);

            if ($selected) {
                echo nonaki_e(Render::select('nonaki_sub_type', $selected, ($sub_types_data)));
            } else {
                echo nonaki_e(Render::select('nonaki_sub_type', 'general', ($sub_types_data)));
            }
        }
        wp_die();
    }
}
