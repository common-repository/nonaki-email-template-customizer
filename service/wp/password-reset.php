<?php

namespace Nonaki\Services\WP;

defined('ABSPATH') || exit;

class Password_Reset
{
    use Base;

    public function filter($message, $template_content)
    {
        $filters = [
            '{content}' => $message,
        ];

        return strtr($template_content, $filters);
    }



    public function add_elements($template_id, $type, $sub_type)
    {
        if ($type == $this->type && $sub_type == 'password_reset') {
?>
            <script type="module">
                var blockManager = nonaki.BlockManager;

                blockManager.add('wp-user', {
                    category: 'WordPress',
                    label: `User Name`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-user',
                    },
                    content: `<mj-text>{{user_login}}</mj-text>`,

                });

                blockManager.add('wp-password-reset-link', {
                    category: 'WordPress',
                    label: `Password reset link`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-link',
                    },
                    content: `<mj-text><a href="{{password_reset_link}}">{{password_reset_link}}</a></mj-text>`,

                });
            </script>
<?php
        }
    }

    public static function get_template()
    {
        $args = array(
            'post_type'      => 'nonaki',
            'no_found_rows'  => true,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => 'template_type',
                    'value'   => 'wordpress',
                ),
                array(
                    'key'     => 'template_sub_type',
                    'value'   => 'password_reset',
                ),
                array(
                    'key'     => 'nonaki_status',
                    'value'   => 'active',
                ),
            ),
        );

        $query = new \WP_Query($args);
        if ($query->posts) {

            return  get_post_meta($query->posts[0]->ID, 'compiled_content', true);
        }

        return null;
    }

    public static function filter_message($args)
    {
        return strtr(self::get_template(), $args);
    }
}

add_action('nonaki_editor_scripts', function ($template_id, $type, $sub_type) {
    Password_Reset::get_instance()->add_elements($template_id, $type, $sub_type);
}, 10, 3);



add_filter('retrieve_password_message', function ($message, $key, $user_login, $user_data) {
    $args = [
        '{{content}}' => $message,
        '{{key}}' => $key,
        '{{user_login}}' => $user_login,
        '{{user_data}}' => $user_data,
        '{{password_reset_link}}' => network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login'),
    ];
    if (Password_Reset::get_template()) {
        return Password_Reset::get_instance()->filter_message($args);
    }
    return $message;
}, 10, 4);
