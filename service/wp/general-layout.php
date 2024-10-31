<?php

namespace Nonaki\Services\WP;

defined('ABSPATH') || exit;


class Nonaki_WP
{
    use Base;

    public function get_template()
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
                    'value'   => 'default',
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
    public function filter($message, $template_content)
    {
        $filters = [
            '{{content}}' => $message,
        ];

        return strtr($template_content, $filters);
    }
    public function filter_message($args)
    {

        $message = $args['message'];
        if ($this->get_template()) {
            $message = $this->filter($message, $this->get_template());
        }
        return $message;
    }


    public function password_reset_filter($args)
    {
        $this->get_template();
        $message = $args['message'];
        if ($this->get_template()) {
            $message = str_replace('{{content}}', $message, $this->get_password_rest_template());
        }
        return $message;
    }



    public function init($template_id)
    {
        $this->template_id = $template_id;
    }

    public function add_elements($template_id, $type, $sub_type)
    {
        if ($this->type == 'wordpress')
?>
        <script type="module">
            var blockManager = nonaki.BlockManager;
            // blockManager.add('wp-content', {
            //     category: 'WordPress',
            //     label: `Content`,
            //     editable: true,
            //     attributes: {
            //         class: 'fa fa-wordpress',
            //     },
            //     content: `<mj-text>{{content}}</mj-text>`,

            // });



            blockManager.add('first-name', {
                category: 'WordPress',
                label: `First Name`,
                editable: true,
                attributes: {
                    class: 'fa fa-wordpress',
                },
                content: `<mj-text>{{first_name}}</mj-text>`,

            });

            blockManager.add('last-name', {
                category: 'WordPress',
                label: `Last Name`,
                editable: true,
                attributes: {
                    class: 'fa fa-wordpress',
                },
                content: `<mj-text>{{last_name}}</mj-text>`,

            });

            blockManager.add('unsubscribe-url', {
                category: 'WordPress',
                label: `Unsubscribe URL`,
                editable: true,
                attributes: {
                    class: 'fa fa-wordpress',
                },
                content: `<mj-text>{{unsubscribe_url}}</mj-text>`,

            });

            blockManager.add('wp-site-url', {
                category: 'WordPress',
                label: `Site URL`,
                editable: true,
                attributes: {
                    class: 'fa fa-wordpress',
                },
                content: `<mj-text>{{site_url}}</mj-text>`,

            });
        </script>
<?php

}
}

add_action('nonaki_editor_scripts', function ($template_id, $type, $sub_type) {
    Nonaki_WP::get_instance()->add_elements($template_id, $type, $sub_type);
}, 10, 3);

add_filter('wp_mail_content_type', function () {
    return "text/html";
});

// add_filter('wp_mail', function ($args) {
//     $args['message'] = Nonaki_WP::get_instance()->filter_message($args);
//     return $args;
// });
