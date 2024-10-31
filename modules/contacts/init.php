<?php

/**
 * ---------------------------------
 * This module is responsible to create new contact and modify them.
 * Also this module will help to send email to the the contacts.
 */

namespace Nonaki\Modules;

use Nonaki\BaseTrait;

class Contacts
{
    use BaseTrait;
    private $sent_count =  0;
    public function init()
    {
        add_action('init', [$this, 'nonaki_contacts'], 0);
        add_action('init', [$this, 'list_init']);
        add_filter('enter_title_here', [$this, 'change_title_text']);
        add_action('add_meta_boxes', [$this, 'contact_meta_box']);
        add_action('save_post', [$this, 'contact_save_meta_box']);
        add_filter('manage_nonaki_contact_posts_columns', [$this, 'contact_columns']);
        add_action('manage_nonaki_contact_posts_custom_column', [$this, 'contact_column_content'], 10, 2);
        add_action('restrict_manage_posts', [$this, 'custom_contact_filter_by_subscription_status']);
        add_filter('parse_query', [$this, 'custom_contact_filter_by_subscription_status_query']);
        add_filter('bulk_actions-edit-nonaki_contact', [$this, 'custom_contact_bulk_actions']);
        add_action('admin_action_send_email', [$this, 'contact_handle_send_email_action']);
        add_filter('post_updated_messages', [$this, 'rw_post_updated_messages']);
        add_action('admin_enqueue_scripts', [$this, 'assets']);
        add_action('admin_notices', [$this, 'custom_contact_email_sent_message']);
        add_action('admin_notices', [$this, 'rewrite_cpt_header']);
        add_action('wp_ajax_export_contacts', [$this, 'export_contacts_csv']);
    }

    public function rewrite_cpt_header()
    {
        $screen = get_current_screen();
        $export_url = add_query_arg(
            array(
                'action' => 'export_contacts',

            ),
            admin_url('admin-ajax.php')
        );

        if ($screen->id != 'edit-nonaki_contact') {
            return;
        } else {
?>
            <div class="wrap">
                <h1 class="wp-heading-inline show" style="display:inline-block;">Contacts</h1>
                <a href="<?php echo admin_url('post-new.php?post_type=nonaki_contact'); ?>" class="page-title-action show">Add New Contact</a>
                <a href="<?php echo $export_url ?>" class="page-title-action show">Export Contacts (CSV)</a>
            </div>

            <style scoped>
                .wp-heading-inline:not(.show),
                .page-title-action:not(.show) {
                    display: none !important;
                }
            </style>
        <?php
        }
    }

    public function export_contacts_csv()
    {
        $meta_keys = array(
            'first_name',
            'last_name',
            'subscription_status',
            'source',
        );
        nonaki_export_to_csv('nonaki_contact', $meta_keys);
    }

    public function assets($hook)
    {

        if (get_current_screen()->id == 'edit-nonaki_contact') {
            $script_handle =  'nonaki-contact-script';
            wp_enqueue_script(
                $script_handle,
                NONAKI_ASSETS_URL . '/parts/js/contact.js',
                ['jquery'],
                false,
                false
            );

            wp_localize_script($script_handle, 'nonaki_contact_data', [
                'template_selection_dropdown' => $this->contact_action_template_selection_dropdown()
            ]);
        }
    }

    public function rw_post_updated_messages($messages)
    {
        $messages['nonaki_contact'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __('Contact updated.'),

        );
        return $messages;
    }

    // Show success message after sending email
    public function custom_contact_email_sent_message()
    {
        if (isset($_GET['email_sent']) && $_GET['email_sent'] == 'true') {
            $message = '<div class="updated"><p>' . __('Email sent successfully.', 'text_domain') . '</p></div>';
            echo $message;
        }
    }


    // Handle Send Email Action with selected template
    public function contact_handle_send_email_action()
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'send_email') {

            $email_template_id = isset($_REQUEST['email_template_id']) ? $_REQUEST['email_template_id'] : '';
            $mail_subject      = isset($_REQUEST['email_subject']) ? $_REQUEST['email_subject'] : '';
            $email_type        =  isset($_REQUEST['email_type']) ? $_REQUEST['email_type'] : 'text';
            $custom_message    = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
            $contact_ids       = isset($_REQUEST['post']) ? $_REQUEST['post'] : array();
            $result            = null;
            $sent_count        = 0;

            foreach ($contact_ids as $contact_id) {
                $contact      = get_post($contact_id);
                $first_name   = get_post_meta($contact_id, 'first_name', true);
                $last_name    = get_post_meta($contact_id, 'last_name', true);
                $to           = $contact->post_title;
                $current_user = wp_get_current_user();
                $from         = $current_user->user_email;
                $subject      = empty($mail_subject) ? 'Mail Subject' : $mail_subject;
                $message      = $custom_message;
                $filter = [
                    '{{first_name}}' => $first_name,
                    '{{last_name}}' => $last_name,
                    '{{site_url}}' => get_site_url(),
                ];
                $subject = strtr($subject, $filter);

                if ($email_type === 'template') {
                    if ($email_template_id) {
                        $result = nonaki_send_email($to, $from, $subject, $email_template_id, 'wp', null, $filter);
                    }
                }

                if ($email_type === 'text') {
                    $message = strtr($message, $filter);
                    $result = nonaki_send_email($to, $from, $subject, $message, 'wp', 'text');
                }


                $sent_count++;
            }

            if ($result) {
                $redirect_to = add_query_arg('email_sent', 'true', $_SERVER['HTTP_REFERER']);
                nonaki_set_total_mail_sent($sent_count);
                wp_safe_redirect($redirect_to);
                exit;
            }
        }
    }

    // Add Select Email Templates dropdown to Send Email action
    public function contact_action_template_selection_dropdown()
    {

        $selection_content = '';

        $nonaki_query = new \WP_Query(array(
            'post_type' => 'nonaki',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
        ));

        $selection_content .= '<select id="email_type" name="email_type">';
        $selection_content .= '<option value="">' . __('Select Email Type', 'nonaki') . '</option>';
        $selection_content .= '<option value="template">Template</option><option value="text">Text</option>';
        $selection_content .= '</select>';


        if ($nonaki_query->have_posts()) {

            $selection_content .= '<select style="display:none" id="email_template_id" name="email_template_id">';
            $selection_content .= '<option value="">' . __('Select Email Template', 'nonaki') . '</option>';
            while ($nonaki_query->have_posts()) {
                $nonaki_query->the_post();
                $selection_content .= '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
            }
            $selection_content .= '</select>';
        } else {
            $selection_content .= '<a href="' . admin_url('/admin.php?page=nonaki-choice-type') . '" style="display:none;margin: 0px 10px;" id="email_template_id">Create Template</a>';
        }


        $selection_content .= '<div style="display: inline-flex;
        gap: 6px;
        margin-bottom: 10px;">';
        $selection_content .= '<input style="
        height: 30px; display:none;
    " placeholder="Mail subject" type="text" id="email_subject" name="email_subject">';
        $selection_content .= '<textarea style="display:none" placeholder="Your message" id="message" name="message"></textarea>';
        $selection_content .= '</div>';
        wp_reset_postdata();
        return $selection_content;
    }

    public function custom_contact_bulk_actions($actions)
    {
        $actions['send_email'] = __('Send Email', 'text_domain');
        return $actions;
    }

    public function custom_contact_filter_by_subscription_status()
    {
        global $typenow, $wp_query;
        if ($typenow != 'nonaki_contact') {
            return;
        }
        $statuses = array('pending', 'subscribe', 'unsubscribe');
        $current_status = isset($_GET['subscription_status']) ? $_GET['subscription_status'] : '';

        $source_list = nonaki_get_contacts_source_list();
        $current_source = isset($_GET['source']) ? $_GET['source'] : '';
        ?>
        <select name="subscription_status">
            <option value=""><?php _e('All Subscription Statuses', 'nonaki'); ?></option>
            <?php foreach ($statuses as $status) : ?>
                <option value="<?php echo esc_attr($status); ?>" <?php selected($current_status, $status); ?>><?php echo ucfirst($status); ?></option>
            <?php endforeach; ?>
        </select>

        <select name="source">
            <option value=""><?php _e('All Contact Source', 'nonaki'); ?></option>
            <?php foreach ($source_list as $source) : ?>
                <option value="<?php echo esc_attr($source); ?>" <?php selected($current_source, $source); ?>><?php echo nonaki_get_contacts_source()[$source]; ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }

    // Modify Subscription Status Query
    public function custom_contact_filter_by_subscription_status_query($query)
    {
        global $pagenow, $typenow;

        $meta_query = array();

        if ($typenow == 'nonaki_contact' && $pagenow == 'edit.php' && isset($_GET['subscription_status']) && !empty($_GET['subscription_status'])) {
            // $query->query_vars['meta_key'] = 'subscription_status';
            // $query->query_vars['meta_value'] = $_GET['subscription_status'];

            $meta_query[] = array(
                'key' => 'subscription_status',
                'value'    => $_GET['subscription_status'],
                'compare' => '=',
            );
        }

        if ($typenow == 'nonaki_contact' && $pagenow == 'edit.php' && isset($_GET['source']) && !empty($_GET['source'])) {
            // $query->query_vars['meta_key'] = 'source';
            // $query->query_vars['meta_value'] = $_GET['source'];
            $meta_query[] = array(
                'key' => 'source',
                'value'    => $_GET['source'],
                'compare' => '=',
            );
        }
        $query->set('meta_query', $meta_query);
    }


    public function contact_column_content($column_name, $post_id)
    {

        if ('subscription_status' == $column_name) {
            $status = get_post_meta($post_id, 'subscription_status', true);
            switch ($status) {
                case 'pending':
                    echo __('Pending', 'nonaki');
                    break;
                case 'subscribe':
                    echo __('Subscribed', 'nonaki');
                    break;
                case 'unsubscribe':
                    echo __('Unsubscribed', 'nonaki');
                    break;
                default:
                    echo __('Unknown', 'nonaki');
                    break;
            }
        }



        if ('list' == $column_name) {
            $terms = get_the_terms($post_id, 'list');

            if (!empty($terms)) {
                $out = array();
                foreach ($terms as $term) {
                    $out[] = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post_type' => 'nonaki_contact', 'list' => $term->slug), 'edit.php')),
                        esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'list', 'display'))
                    );
                }
                echo join(', ', $out);
            } else {
                _e('No Lists', 'nonaki');
            }
        }

        if ('first_name' == $column_name) {
            $first_name = get_post_meta($post_id, 'first_name', true);
            echo esc_html($first_name);
        }
        if ('last_name' == $column_name) {
            $last_name = get_post_meta($post_id, 'last_name', true);
            echo esc_html($last_name);
        }

        if ('source' == $column_name) {
            $source = get_post_meta($post_id, 'source', true);
            echo __(nonaki_get_contacts_source()[$source], 'nonaki');
        }
    }

    public function contact_columns($columns)
    {
        $columns['first_name'] = __('First Name', 'nonaki');
        $columns['last_name'] = __('Last Name', 'nonaki');
        $columns['list'] = __('Lists', 'nonaki');
        $columns['subscription_status'] = __('Subscription Status', 'nonaki');
        $columns['source'] = __('Contact Source', 'nonaki');
        unset($columns['date']);
        // $columns['date'] = __('Date', 'nonaki');
        return $columns;
    }


    public function contact_meta_box()
    {
        add_meta_box(
            'custom_contact_meta_box',
            __('Contact Information', 'nonaki'),
            [$this, 'contact_meta_box_callback'],
            'nonaki_contact',
            'normal',
            'high'
        );
    }

    function contact_meta_box_callback($post)
    {
        wp_nonce_field(basename(__FILE__), 'custom_contact_nonce');

        $first_name = get_post_meta($post->ID, 'first_name', true);
        $last_name = get_post_meta($post->ID, 'last_name', true);
        $subscription_status = get_post_meta($post->ID, 'subscription_status', true);
        $source = get_post_meta($post->ID, 'source', true);
    ?>

        <div style="width:100%;display:flex;justify-content: space-evenly;">
            <div style="width:100%">
                <p>
                    <label for="first_name"><?php _e('First Name', 'nonaki'); ?></label><br />
                    <input type="text" style="margin-top: 5px;" name="first_name" id="first_name" value="<?php echo esc_attr($first_name); ?>" />
                </p>
                <p>
                    <label for="last_name"><?php _e('Last Name', 'nonaki'); ?></label><br />
                    <input type="text" style="margin-top: 5px;" name="last_name" id="last_name" value="<?php echo esc_attr($last_name); ?>" />
                </p>

            </div>
            <div style="width:100%">
                <p>
                    <label for="subscription_status"><?php _e('Subscription Status', 'nonaki'); ?></label><br />
                    <select style="margin-top: 5px;" name="subscription_status" id="subscription_status">
                        <option value="pending" <?php selected($subscription_status, 'pending'); ?>><?php _e('Pending', 'nonaki'); ?></option>
                        <option value="subscribe" <?php selected($subscription_status, 'subscribe'); ?>><?php _e('Subscribe', 'nonaki'); ?></option>
                        <option value="unsubscribe" <?php selected($subscription_status, 'unsubscribe'); ?>><?php _e('Unsubscribe', 'nonaki'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="source"><?php _e('Contact Source', 'nonaki'); ?></label><br />
                    <select style="margin-top: 5px;" name="source" id="source">
                        <?php foreach (nonaki_get_contacts_source() as $contact_source => $contact_source_value) { ?>
                            <option value="<?php echo esc_html($contact_source) ?>" <?php selected($source, $contact_source); ?>><?php _e($contact_source_value, 'nonaki'); ?></option>
                        <?php } ?>

                    </select>
                </p>
            </div>
        </div>
<?php
    }

    public function contact_save_meta_box($post_id)
    {
        if (!isset($_POST['custom_contact_nonce']) || !wp_verify_nonce($_POST['custom_contact_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        $post_type = get_post_type($post_id);

        if ('nonaki_contact' == $post_type) {

            if (isset($_POST['first_name'])) {
                update_post_meta($post_id, 'first_name', sanitize_text_field($_POST['first_name']));
            }

            if (isset($_POST['last_name'])) {
                update_post_meta($post_id, 'last_name', sanitize_text_field($_POST['last_name']));
            }

            if (isset($_POST['subscription_status'])) {
                update_post_meta($post_id, 'subscription_status', sanitize_text_field($_POST['subscription_status']));
            }

            if (isset($_POST['source'])) {
                update_post_meta($post_id, 'source', sanitize_text_field($_POST['source']));
            }
        }
    }

    public function change_title_text($title)
    {
        $screen = get_current_screen();

        if ('nonaki_contact' == $screen->post_type) {
            $title = 'Enter Email Address';
        }

        return $title;
    }


    public function list_init()
    {
        $labels = [
            'name'                       => _x('Lists', 'Taxonomy General Name', 'nonaki'),
            'singular_name'              => _x('List', 'Taxonomy Singular Name', 'nonaki'),
            'menu_name'                  => __('Lists', 'nonaki'),
            'all_items'                  => __('All Lists', 'nonaki'),
            'parent_item'                => __('List Item', 'nonaki'),
            'parent_item_colon'          => __('List Item:', 'nonaki'),
            'new_item_name'              => __('New List', 'nonaki'),
            'add_new_item'               => __('Add New List', 'nonaki'),
            'edit_item'                  => __('Edit List', 'nonaki'),
            'update_item'                => __('Update List', 'nonaki'),
            'separate_items_with_commas' => __('Separate Lists with commas', 'nonaki'),
            'search_items'               => __('Search List', 'nonaki'),
            'add_or_remove_items'        => __('Add or remove Lists', 'nonaki'),
            'choose_from_most_used'      => __('Choose from the most used Lists', 'nonaki'),
            'not_found'                  => __('List Not Found', 'nonaki'),
        ];
        // create a new taxonomy
        register_taxonomy(
            'list',
            'nonaki_contact',
            array(
                'labels' => $labels,
                'rewrite' => array('slug' => 'list'),
                // 'capabilities' => array(
                //     'assign_terms' => 'edit_guides',
                //     'edit_terms' => 'publish_guides'
                // )
            )
        );
    }

    public function nonaki_contacts()
    {
        $labels = [
            'name' => _x('Contacts', 'Post Type General Name', 'nonaki'),
            'singular_name' => _x('Contact', 'Post Type Singular Name', 'nonaki'),
            'menu_name' => __('Contacts', 'nonaki'),
            'name_admin_bar' => __('Contacts', 'nonaki'),
            'archives' => __('Contacts Archives', 'nonaki'),
            'attributes' => __('Contacts Attributes', 'nonaki'),
            'parent_item_colon' => __('Parent Contact:', 'nonaki'),
            'all_items' => __('Contacts', 'nonaki'),
            'add_new_item' => __('Add New Contact', 'nonaki'),
            'add_new' => __('Add New', 'nonaki'),
            'new_item' => __('New Contact', 'nonaki'),
            'edit_item' => __('Edit Contact', 'nonaki'),
            'update_item' => __('Update Contact', 'nonaki'),
            'view_item' => __('View Contact', 'nonaki'),
            'view_items' => __('View Contacts', 'nonaki'),
            'search_items' => __('Search Contacts', 'nonaki'),
            'not_found' => __('Contact Not Found', 'nonaki'),
            'not_found_in_trash' => __('Contact Not Found in Trash', 'nonaki'),
            'featured_image' => __('Featured Image', 'nonaki'),
            'set_featured_image' => __('Set Featured Image', 'nonaki'),
            'remove_featured_image' => __('Remove Featured Image', 'nonaki'),
            'use_featured_image' => __('Use as Featured Image', 'nonaki'),
            'insert_into_item' => __('Insert into Contact', 'nonaki'),
            'uploaded_to_this_item' => __('Uploaded to this Contact', 'nonaki'),
            'items_list' => __('Contacts List', 'nonaki'),
            'items_list_navigation' => __('Contacts List Navigation', 'nonaki'),
            'filter_items_list' => __('Filter Contacts List', 'nonaki'),
            'item_updated' => __('Contact updated', 'nonaki'),
        ];
        $labels = apply_filters('nonaki_contact-labels', $labels);

        $args = [
            'label' => __('Contact', 'nonaki'),
            'labels' => $labels,
            'supports' => [
                'title',
            ],
            'taxonomies' => [
                'list',
                // 'tags',
            ],
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 3,
            'menu_icon' => 'dashicons-admin-post',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'exclude_from_search' => true,
            'has_archive' => true,
            'can_export' => true,
            'capability_type' => 'post',
            'show_in_rest' => false,
        ];
        $args = apply_filters('nonaki_contact-args', $args);

        register_post_type('nonaki_contact', $args);
    }
}
