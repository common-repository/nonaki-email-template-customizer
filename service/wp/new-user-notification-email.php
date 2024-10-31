<?php

namespace Nonaki\Services\WP;

defined('ABSPATH') || exit;
class New_User_Notification_Email
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
        if ($type == $this->type && $sub_type == 'new_user_notification_email') {
?>
            <script type="module">
                var blockManager = nonaki.BlockManager;

                blockManager.add('wp-user-to', {
                    category: 'WordPress',
                    label: `New User Email`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-envelope',
                    },
                    content: `<mj-text>{{to}}</mj-text>`,

                });

                blockManager.add('wp-user-first-name', {
                    category: 'WordPress',
                    label: `First Name`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-user',
                    },
                    content: `<mj-text>{{first_name}}</mj-text>`,

                });

                blockManager.add('wp-user-last-name', {
                    category: 'WordPress',
                    label: `Last Name`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-user',
                    },
                    content: `<mj-text>{{last_name}}</mj-text>`,

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
                    'value'   => 'new_user_notification_email',
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

    public static function filter_message($args, $user, $blogname)
    {

        $filtered_message = strtr(self::get_template(), [
            '{{to}}' => $args['to'],
            '{{content}}' => $args['message'],
            '{{first_name}}' => $user->first_name,
            '{{last_name}}' => $user->last_name,
            '{{site_url}}' => $blogname,

        ]);
        $args['message'] = $filtered_message;
        return $args;
    }
}

add_filter('nonaki_template_sub_types', function ($args) {
    $args['wordpress']['new_user_notification_email'] = 'New User Notification Email';
    return $args;
});

add_action('nonaki_editor_scripts', function ($template_id, $type, $sub_type) {
    New_User_Notification_Email::get_instance()->add_elements($template_id, $type, $sub_type);
}, 10, 3);

add_filter('wp_new_user_notification_email', function ($wp_new_user_notification_email, $user, $blogname) {

    if (New_User_Notification_Email::get_template()) {
        return New_User_Notification_Email::get_instance()->filter_message($wp_new_user_notification_email, $user, $blogname);
    }
    return $wp_new_user_notification_email;
}, 10, 3);
