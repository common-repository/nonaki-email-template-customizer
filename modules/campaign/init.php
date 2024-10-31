<?php

namespace Nonaki\Modules;

use Nonaki\BaseTrait;

class Campaign
{
    use BaseTrait;

    public function init()
    {
        add_action('init', [$this, 'nonaki_campaign_post_type']);
        add_action('add_meta_boxes', [$this, 'nonaki_campaign_metaboxes']);
        add_action('save_post_nonaki_campaign', [$this, 'nonaki_campaign_metabox_save']);
        add_action('add_meta_boxes', [$this, 'nonaki_campaign_add_template_metabox']);
        add_action('save_post', [$this, 'nonaki_campaign_save_template_metabox']);
        add_action('admin_enqueue_scripts', [$this, 'assets']);
        add_filter('post_updated_messages', [$this, 'rw_post_updated_messages']);
    }

    public function assets()
    {

        if (get_current_screen()->id == 'nonaki_campaign') {
            $script_handle =  'nonaki-campaign-script';
            wp_enqueue_script(
                $script_handle,
                NONAKI_URL . '/modules/campaign/assets/js/script.js',
                ['jquery'],
                false,
                false
            );

            wp_enqueue_style(
                'nonaki-campaign-style',
                NONAKI_URL .  '/modules/campaign/assets/css/style.css',
                [],
                false
            );

            wp_localize_script($script_handle, 'nonaki_campaign_data', [
                'admin_url' => admin_url('')
            ]);
        }
    }

    public function nonaki_campaign_save_template_metabox($post_id)
    {
        if (!isset($_POST['nonaki_campaign_template_metabox_nonce']) || !wp_verify_nonce($_POST['nonaki_campaign_template_metabox_nonce'], basename(__FILE__))) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        if (isset($_POST['template_id'])) {
            update_post_meta($post_id, 'template_id', absint($_POST['template_id']));
        } else {
            delete_post_meta($post_id, 'template_id');
        }
    }

    public function nonaki_campaign_add_template_metabox()
    {
        add_meta_box(
            'nonaki_campaign_template_metabox',
            __('Email Template', 'nonaki'),
            [$this, 'nonaki_campaign_template_metabox_html'],
            'nonaki_campaign',
            'normal',
            'default'
        );
    }

    public function rw_post_updated_messages($messages)
    {
        $messages['nonaki_campaign'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __('Campaign updated.'),

        );
        return $messages;
    }

    // The HTML for the metabox
    public function nonaki_campaign_template_metabox_html($post)
    {
        wp_nonce_field(basename(__FILE__), 'nonaki_campaign_template_metabox_nonce');
        $template_id = get_post_meta($post->ID, 'template_id', true);
        $templates = get_posts(array('post_type' => 'nonaki', 'numberposts' => -1));
        $preview_link = admin_url('/index.php?page=nonaki&id=' . $template_id . '&mood=preview');
?>
        <div style="margin-bottom:10px">
            <label for=" template_id"><?php _e('Select an email template:', 'nonaki'); ?></label>
            <select name="template_id" id="template_id">
                <option value=""><?php _e('Select an email template', 'nonaki'); ?></option>
                <?php foreach ($templates as $template) : ?>
                    <option value="<?php echo esc_attr($template->ID); ?>" <?php selected($template_id, $template->ID); ?>>
                        <?php echo esc_html($template->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php
        echo nonaki_preview_iframe($preview_link);
    }

    public function nonaki_campaign_metabox_save($post_id)
    {
        if (!isset($_POST['nonaki_campaign_metabox_nonce']) || !wp_verify_nonce($_POST['nonaki_campaign_metabox_nonce'], basename(__FILE__))) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['post_type']) && 'nonaki_campaign' === $_POST['post_type']) {
            if (current_user_can('edit_post', $post_id)) {
                update_post_meta($post_id, '_nonaki_campaign_subscription_status', sanitize_text_field($_POST['nonaki_campaign_subscription_status']));
                update_post_meta($post_id, '_nonaki_campaign_contact_source', sanitize_text_field($_POST['nonaki_campaign_contact_source']));
            }
        }
    }

    public function nonaki_campaign_metaboxes()
    {
        add_meta_box(
            'nonaki_campaign_metabox',
            __('Filter', 'nonaki'),
            [$this, 'nonaki_campaign_metabox_callback'],
            'nonaki_campaign',
            'side',
            'default'
        );
    }

    public function nonaki_campaign_metabox_callback($post)
    {
        wp_nonce_field(basename(__FILE__), 'nonaki_campaign_metabox_nonce');

        // Get saved values
        $subscription_status = get_post_meta($post->ID, '_nonaki_campaign_subscription_status', true);
        $contact_source = get_post_meta($post->ID, '_nonaki_campaign_contact_source', true);
    ?>
        <p>
            <label for="nonaki_campaign_subscription_status"><?php _e('Subscription Status', 'nonaki'); ?>:</label>
            <select name="nonaki_campaign_subscription_status" id="nonaki_campaign_subscription_status">
                <option value=""><?php _e('Select Subscription Status', 'nonaki'); ?></option>
                <option value="pending" <?php selected($subscription_status, 'pending'); ?>><?php _e('Pending', 'nonaki'); ?></option>
                <option value="subscribed" <?php selected($subscription_status, 'subscribed'); ?>><?php _e('Subscribed', 'nonaki'); ?></option>
                <option value="unsubscribed" <?php selected($subscription_status, 'unsubscribed'); ?>><?php _e('Unsubscribed', 'nonaki'); ?></option>
            </select>
        </p>
        <p>
            <label for="nonaki_campaign_contact_source"><?php _e('Contact Source', 'nonaki'); ?>:</label>
            <select name="nonaki_campaign_contact_source" id="nonaki_campaign_contact_source">
                <option value=""><?php _e('Select Contact Source', 'nonaki'); ?></option>
                <option value="custom" <?php selected($contact_source, 'custom'); ?>><?php _e('Custom', 'nonaki'); ?></option>
                <option value="visitor" <?php selected($contact_source, 'visitor'); ?>><?php _e('Visitor', 'nonaki'); ?></option>
            </select>
        </p>
<?php
    }

    public function nonaki_campaign_post_type()
    {
        $labels = array(
            'name'               => __('Campaigns', 'nonaki'),
            'singular_name'      => __('Campaign', 'nonaki'),
            'add_new'            => __('Add New', 'nonaki'),
            'add_new_item'       => __('Add New Campaign', 'nonaki'),
            'edit_item'          => __('Edit Campaign', 'nonaki'),
            'new_item'           => __('New Campaign', 'nonaki'),
            'view_item'          => __('View Campaign', 'nonaki'),
            'search_items'       => __('Search Campaigns', 'nonaki'),
            'not_found'          => __('No Campaigns found', 'nonaki'),
            'not_found_in_trash' => __('No Campaigns found in Trash', 'nonaki'),
            'parent_item_colon'  => __('Parent Campaign:', 'nonaki'),
            'menu_name'          => __('Campaigns', 'nonaki'),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __('campaigns', 'nonaki'),
            'public'             => false,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'taxonomies' => [
                'list',
                // 'tags',
            ],
            'rewrite'            => array('slug' => 'nonaki_campaign'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title'),
            'menu_icon'          => 'dashicons-email-alt',
        );

        register_post_type('nonaki_campaign', $args);
    }
}
