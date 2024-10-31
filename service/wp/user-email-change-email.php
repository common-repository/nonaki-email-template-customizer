<?php

namespace Nonaki\Services\WP;



defined('ABSPATH') || exit;

class User_Email_Change_Email
{
    use Base;

    public function filter($message, $template_content)
    {
        $filters = [
            '{{content}}' => $message,
        ];

        return strtr($template_content, $filters);
    }

    public function add_elements($template_id, $type, $sub_type)
    {
        if ($type == $this->type && $sub_type == 'email_change_email') {
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
                    label: `User Name`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-user',
                    },
                    content: `<mj-text>{{user_name}}</mj-text>`,

                });


                blockManager.add('wp-admin-mail', {
                    category: 'WordPress',
                    label: `Admin Email`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-envelope',
                    },
                    content: `<mj-text>{{admin_mail}}</mj-text>`,

                });

                blockManager.add('wp-new-mail', {
                    category: 'WordPress',
                    label: `New Email`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-envelope',
                    },
                    content: `<mj-text>{{new_mail}}</mj-text>`,

                });

                blockManager.add('wp-old-mail', {
                    category: 'WordPress',
                    label: `Old Email`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-envelope',
                    },
                    content: `<mj-text>{{old_mail}}</mj-text>`,

                });

                blockManager.add('wp-site-url', {
                    category: 'WordPress',
                    label: `Old Email`,
                    editable: true,
                    attributes: {
                        class: 'fa fa-link',
                    },
                    content: `<mj-text>{{site_url}}</mj-text>`,

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
                    'value'   => 'email_change_email',
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

    public static function filter_message($args, $user, $userdata)
    {

        $filtered_message = strtr(self::get_template(), [
            '{{to}}' => $args['to'],
            '{{content}}' => $args['message'],
            '{{user_name}}' => '###USERNAME###',
            '{{admin_mail}}' => '###ADMIN_EMAIL###',
            '{{new_mail}}' => '###NEW_EMAIL###',
            '{{old_mail}}' => '###EMAIL###',
            '{{site_name}}' => '###SITENAME###',
            '{{site_url}}' => '###SITEURL###',

        ]);

        $args['message'] = $filtered_message;
        return $args;
    }
}

add_filter('nonaki_template_sub_types', function ($args) {
    $args['wordpress']['email_change_email'] = 'User Email Change Email';
    return $args;
});

add_action('nonaki_editor_scripts', function ($template_id, $type, $sub_type) {
    User_Email_Change_Email::get_instance()->add_elements($template_id, $type, $sub_type);
}, 10, 3);

add_filter('email_change_email', function ($email_change_email, $user, $userdata) {

    if (User_Email_Change_Email::get_template()) {
        return User_Email_Change_Email::get_instance()->filter_message($email_change_email, $user, $userdata);
    }
    return $email_change_email;
}, 10, 3);
