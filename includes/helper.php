<?php

use Nonaki\Modules\Email\Email;
use Nonaki\Modules\Email\Mailgun;
use Nonaki\Modules\Email\WP_Mail_Service;
use Nonaki\Modules\Templates;
use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

if (!function_exists('nonaki_save_editor_content')) {

    function nonaki_save_editor_content($post_data)
    {

        // $content = wp_kses($post_data['content'], nonaki_prefix_allowed_tags_all());
        $content = $post_data['content'];
        if (isset($post_data['action'])) {
            if (isset($post_data['postID'])) {

                if ($post_data['postID'] == 'new') {
                    $data = [
                        'post_title' => '(change title)',
                        'post_content' => $content,
                        'post_type' => 'nonaki',
                        'post_status'   => 'publish',

                        'meta_input'   => array(
                            'template_type' => 'general',
                        ),
                    ];
                }
            }
        }
        if (isset($post_data['title'])) {

            $data = [
                'ID'           => sanitize_text_field($post_data['postID']),
                'post_title'   => sanitize_text_field($post_data['title']),
                'post_content' => $content,
                'meta_input' => array(
                    'content_type' => sanitize_text_field($post_data['contentType']),
                    'compiled_content' => $post_data['compiledContent'],
                )
            ];

            wp_update_post($data);
        }
    }
}

if (!function_exists('nonaki_preview_iframe')) {
    function nonaki_preview_iframe($preview_link)
    {
        return '<div style="margin:0px;padding:0px;overflow:hidden;height:70vh">
        <iframe id="nonaki-iframe" src="' . $preview_link . '" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
        </div>';
    }
}

if (!function_exists('nonaki_send_email')) {
    function nonaki_send_email($to, $from, $subject, $templateID, $provider, $type = null, $filter = null)
    {

        require_once NONAKI_DIR . '/modules/email/email.php';
        $email = new Email();

        $mailgun_config = [
            'domain' => get_option('nonaki_provider_mailgun_domain'),
            'api'    => get_option('nonaki_provider_mailgun_api'),
        ];

        $providers = [
            'mailgun' => new Mailgun($mailgun_config),
            'wp'      => new WP_Mail_Service,
        ];

        if (!isset($providers[$provider])) {
            throw new Exception('Email provider does not exists');
        }

        $content = '';
        if ($type === 'text') {
            $content = $templateID;
        } else {
            $content = nonaki_get_email_template($templateID);

            // If filter available for this email then apply it
            if ($filter) {
                $content = strtr($content, $filter);
            }
        }

        $result = $email->to($to)
            ->from($from)
            ->set_subject($subject)
            ->set_content($content)
            ->set_provider($providers[$provider])
            ->send();

        return $result;
    }
}

if (!function_exists('nonaki_get_email_template')) {
    function nonaki_get_email_template($templateID)
    {
        $content_type = get_post_meta($templateID, 'content_type', true);
        if ($content_type === 'mail') {
            return get_post_meta($templateID, 'compiled_content', true);
        } else {
            return get_post_field('post_content', $templateID);
        }
    }
}

if (!function_exists('nonaki_set_total_mail_sent')) {
    function nonaki_set_total_mail_sent($mail_count)
    {
        $option_name = 'nonaki_total_email_sent';
        if (get_option($option_name)) {
            $total = get_option($option_name) + $mail_count;
            update_option($option_name, $total);
        } else {
            update_option($option_name, $mail_count);
        }
    }
}

if (!function_exists('nonaki_get_total_mail_sent')) {
    function nonaki_get_total_mail_sent()
    {
        $option_name = 'nonaki_total_email_sent';
        if (get_option($option_name)) {
            return get_option($option_name);
        }
        return 0;
    }
}

if (!function_exists('nonaki_get_contacts_source')) {
    function nonaki_get_contacts_source()
    {
        $source = array(
            'custom' => 'Custom',
            'visitor' => 'Visitor',
            'social_media' => 'Social Media',
            'advertisement' => 'Advertisement',
            'referrals' => 'Referrals',
        );

        $all_sources = apply_filters('nonaki_contact_source', $source);
        return $all_sources;
    }
}

if (!function_exists('nonaki_get_contacts_source_list')) {
    function nonaki_get_contacts_source_list()
    {
        $source_list = [];
        foreach (nonaki_get_contacts_source() as $source_key => $source_value) {
            array_push($source_list, $source_key);
        }

        return $source_list;
    }
}


if (!function_exists('add_nonaki_email_preset')) {
    function add_nonaki_email_preset($id, $name, $image, $content)
    {

        add_filter('nonaki_email_presets', function ($templates) use ($id, $name, $image, $content) {
            $templates[$id] = [
                'name' => $name,
                'image' => $image,
                'content' => $content
            ];

            return $templates;
        });
    }
}

if (!function_exists('nonaki_get_presets_url')) {
    function nonaki_get_presets_url()
    {
        return Templates::get_presets_url();
    }
}

if (!function_exists('nonaki_get_assets_url')) {
    function nonaki_get_assets_url()
    {
        return get_site_url(null, '/wp-json/nonaki/v1/get/media');
    }
}

if (!function_exists('nonaki_manifest')) {
    function nonaki_manifest()
    {
    }
}

if (!function_exists('nonaki_content_merge')) {
    function nonaki_content_merge($template_id, $content)
    {
        $n_filters = [
            '{content}' => $content,
            '{developer_name}' => 'Prappo'
        ];

        $n_email_content = nonaki_get_email_template($template_id);
        return str_replace(array_keys($n_filters), $n_filters, $n_email_content);
    }
}

if (!function_exists('nonaki_get_option')) {
    function nonaki_get_option($option_name)
    {
    }
}

if (!function_exists('nonaki_get_woo_templates_list')) {
    function nonaki_get_woo_templates_list()
    {
        return [
            'new_order' => 'New order',
            'cancelled_order' => 'Cancelled order',
            'failed_order' => 'Failed order',
            'customer_on_hold_order' => 'Order on-hold',
            'customer_processing_order' => 'Processing order',
            'customer_completed_order' => 'Completed order',
            'customer_refunded_order' => 'Refunded order',
            'customer_invoice' => 'Customer invoice / Order details',
            'customer_note' => 'Customer note',
            'customer_reset_password' => 'Reset password',
            'customer_new_account' => 'New account',
        ];
    }
}
if (!function_exists('nonaki_get_template_types')) {
    function nonaki_get_template_types()
    {

        $types = [
            'general' => 'General Email Template',
            'wordpress' => 'WordPress default email templates',
        ];

        $result = apply_filters('nonaki_template_types', $types);
        return $result;
    }
}

if (!function_exists('nonaki_get_template_type_from_post_type')) {
    function nonaki_get_template_type_from_post_type($post_type)
    {
        $all_types = [
            'general' => 'mail',
            'wordpress' => 'mail',
            'woo' => 'mail',
            'form' => 'form',
            'popup' => 'form',
        ];

        $all_types = apply_filters('nonaki_template_type_from_post_type', $all_types);

        foreach ($all_types as $p_type => $template_type) {
            if ($p_type === $post_type) {
                return $template_type;
            }
        }

        return '';
    }
}
if (!function_exists('nonaki_get_template_sub_types')) {
    function nonaki_get_template_sub_types($type)
    {

        $types = [
            'wordpress' => [
                'default' => 'Default Layout',
                'password_reset' => 'Password Rest Email',
            ]
        ];

        $result = apply_filters('nonaki_template_sub_types', $types);
        if (isset($result[$type])) {

            return $result[$type];
        }

        return null;
    }
}

if (!function_exists('nonaki_get_media_library_images')) {
    function nonaki_get_media_library_images()
    {
        $query_images_args = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
        );

        $query_images = new WP_Query($query_images_args);

        $images = array();
        foreach ($query_images->posts as $image) {
            $images[] = wp_get_attachment_url($image->ID);
        }
        return $images;
    }
}

if (!function_exists('nonaki_get_short_name')) {
    function nonaki_get_short_name($name)
    {
        $types = [
            'woocommerce' => 'WC',
            'wordpress' => 'WP',
        ];

        $result = apply_filters('nonaki_short_name', $types);
        if (!isset($result[$name])) {
            return strtoupper($name);
        }
        return $result[$name];
    }
}

if (!function_exists('nonaki_woo_exists')) {
    function nonaki_woo_exists()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }

        return false;
    }
}

function nonaki_prefix_allowed_tags_all()
{
    return array(
        // Document metadata.
        'head'  => nonaki_prefix_allowed_global_attributes(),
        'link'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'as'             => true,
                'disabled'       => true,
                'href'           => true,
                'hreflang'       => true,
                'importance'     => true,
                'integrity'      => true,
                'media'          => true,
                'referrerpolicy' => true,
                'rel'            => true,
                'sizes'          => true,
                'title'          => true,
                'type'           => true,
            )
        ),
        'meta'  => array(
            'content' => true,
            'name' => true,
        ),
        'style' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'type'  => true,
                'media' => true,
                'nonce' => true,
                'title' => true,
            )
        ),
        'title' => nonaki_prefix_allowed_global_attributes(),

        // Sectioning root.
        'body' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'onafterprint'     => true,
                'onbeforeprint'    => true,
                'onbeforeunload'   => true,
                'onblur'           => true,
                'onerror'          => true,
                'onfocus'          => true,
                'onhashchange'     => true,
                'onlanguagechange' => true,
                'onload'           => true,
                'onmessage'        => true,
                'onoffline'        => true,
                'ononline'         => true,
                'onpopstate'       => true,
                'onredo'           => true,
                'onresize'         => true,
                'onstorage'        => true,
                'onundo'           => true,
                'onunload'         => true,
            )
        ),

        // Content Sectioning.
        'address'  => nonaki_prefix_allowed_global_attributes(),
        'articles' => nonaki_prefix_allowed_global_attributes(),
        'aside'    => nonaki_prefix_allowed_global_attributes(),
        'footer'   => nonaki_prefix_allowed_global_attributes(),
        'header'   => nonaki_prefix_allowed_global_attributes(),
        'h1'       => nonaki_prefix_allowed_global_attributes(),
        'h2'       => nonaki_prefix_allowed_global_attributes(),
        'h3'       => nonaki_prefix_allowed_global_attributes(),
        'h4'       => nonaki_prefix_allowed_global_attributes(),
        'h5'       => nonaki_prefix_allowed_global_attributes(),
        'h6'       => nonaki_prefix_allowed_global_attributes(),
        'hgroup'   => nonaki_prefix_allowed_global_attributes(),
        'main'     => nonaki_prefix_allowed_global_attributes(),
        'nav'      => nonaki_prefix_allowed_global_attributes(),
        'section'  => nonaki_prefix_allowed_global_attributes(),

        // Text Content.
        'blockquote' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'cite' => true,
            )
        ),
        'dd'         => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'nowrap' => true,
            )
        ),
        'div'        => nonaki_prefix_allowed_global_attributes(),
        'dl'         => nonaki_prefix_allowed_global_attributes(),
        'dt'         => nonaki_prefix_allowed_global_attributes(),
        'figcaption' => nonaki_prefix_allowed_global_attributes(),
        'figure'     => nonaki_prefix_allowed_global_attributes(),
        'hr'         => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'color'   => true,
                'noshade' => true, // Deprecated.
                'size'    => true, // Deprecated.
                'width'   => true, // Deprecated.
            )
        ),
        'li'         => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'value' => true,
            )
        ),
        'ol'         => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'reversed' => true,
                'start'    => true,
            )
        ),
        'p'          => nonaki_prefix_allowed_global_attributes(),
        'pre'        => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'pre' => true,
            )
        ),
        'ul'         => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'compact' => true,
                'type'    => true,
            )
        ),

        // Inline Text Sematics
        'a'      => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'download' => true,
                'href' => true,
                'hreflang' => true,
                'ping' => true,
                'referrerpolicy' => true,
                'rel' => true,
                'target' => true,
                'type' => true,
            )
        ),
        'abbr'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'title' => true,
            )
        ),
        'b'      => nonaki_prefix_allowed_global_attributes(),
        'bdi'    => nonaki_prefix_allowed_global_attributes(),
        'bdo'    => nonaki_prefix_allowed_global_attributes(),
        'br'     => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'clear' => true, // Deprecated.
            )
        ),
        'cite'   => nonaki_prefix_allowed_global_attributes(),
        'code'   => nonaki_prefix_allowed_global_attributes(),
        'data'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'value' => true,
            )
        ),
        'dfn'    => nonaki_prefix_allowed_global_attributes(),
        'em'     => nonaki_prefix_allowed_global_attributes(),
        'i'      => nonaki_prefix_allowed_global_attributes(),
        'kbd'    => nonaki_prefix_allowed_global_attributes(),
        'mark'   => nonaki_prefix_allowed_global_attributes(),
        'q'      => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'cite' => true,
            )
        ),
        'rb'     => nonaki_prefix_allowed_global_attributes(),
        'rp'     => nonaki_prefix_allowed_global_attributes(),
        'rt'     => nonaki_prefix_allowed_global_attributes(),
        'rtc'    => nonaki_prefix_allowed_global_attributes(),
        'ruby'   => nonaki_prefix_allowed_global_attributes(),
        's'      => nonaki_prefix_allowed_global_attributes(),
        'samp'   => nonaki_prefix_allowed_global_attributes(),
        'small'  => nonaki_prefix_allowed_global_attributes(),
        'span'   => nonaki_prefix_allowed_global_attributes(),
        'strong' => nonaki_prefix_allowed_global_attributes(),
        'sub'    => nonaki_prefix_allowed_global_attributes(),
        'sup'    => nonaki_prefix_allowed_global_attributes(),
        'time'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'datetime' => true,
            )
        ),
        'u'      => nonaki_prefix_allowed_global_attributes(),
        'var'    => nonaki_prefix_allowed_global_attributes(),
        'wbr'    => nonaki_prefix_allowed_global_attributes(),

        // Image & Media.
        'area'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'alt'            => true,
                'accesskey'      => true,
                'coords'         => true,
                'download'       => true,
                'href'           => true,
                'hreflang'       => true,
                'media'          => true,
                'name'           => true,
                'nohref'         => true,
                'ping'           => true,
                'referrerpolicy' => true,
                'rel'            => true,
                'shape'          => true,
                'tabindex'       => true,
                'target'         => true,
                'type'           => true,
            )
        ),
        'audio' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'autoplay' => true,
                'buffered' => true,
                'controls' => true,
                'loop'     => true,
                'muted'    => true,
                'played'   => true,
                'preload'  => true,
                'src'      => true,
                'volume'   => true,
            )
        ),
        'img'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'          => true, // Deprecated.
                'alt'            => true,
                'border'         => true, // Deprecated.
                'crossorigin'    => true,
                'decoding'       => true,
                'height'         => true,
                'hspace'         => true, // Deprecated.
                'importance'     => true,
                'intrinsicsize'  => true,
                'ismap'          => true,
                'loading'        => true,
                'longdesc'       => true, // Deprecated.
                'name'           => true, // Deprecated.
                'onerror'        => true,
                'referrerpolicy' => true,
                'sizes'          => true,
                'src'            => true,
                'srcset'         => true,
                'usemap'         => true,
                'vspace'         => true, // Deprecated.
                'width'          => true,
            )
        ),
        'map'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'map' => true,
            )
        ),
        'track' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'default' => true,
                'kind'    => true,
                'label'   => true,
                'src'     => true,
                'srclang' => true,
            )
        ),
        'video' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'autoplay'             => true,
                'autoPictureInPicture' => true,
                'buffered'             => true,
                'controls'             => true,
                'controlslist'         => true,
                'crossorigin'          => true,
                'currentTime'          => true,
                'duration'             => true,
                'height'               => true,
                'intrinsicsize'        => true,
                'loop'                 => true,
                'muted'                => true,
                'playinline'           => true,
                'poster'               => true,
                'preload'              => true,
                'src'                  => true,
                'width'                => true,
            )
        ),

        // Embedded Content.
        'embed'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'height' => true,
                'src'    => true,
                'type'   => true,
                'width'  => true,
            )
        ),
        'iframe'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'           => true,
                'allow'           => true,
                'allowfullscreen' => true,
                'csp'             => true,
                'frameborder'     => true,
                'height'          => true,
                'importance'      => true,
                'loading'         => true,
                'longdesc'        => true,
                'marginheight'    => true,
                'marginwidth'     => true,
                'name'            => true,
                'referrerpolicy'  => true,
                'sandbox'         => true,
                'scrolling'       => true,
                'src'             => true,
                'srcdoc'          => true,
                'width'           => true,
            )
        ),
        'object'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'archive' => true, // Deprecated.
                'border' => true, // Deprecated.
                'classid' => true, // Deprecated.
                'codebase' => true, // Deprecated.
                'codetype' => true, // Deprecated.
                'data' => true,
                'declare' => true, // Deprecated.
                'form' => true,
                'height' => true,
                'name' => true,
                'standby' => true, // Deprecated.
                'tabindex' => true, // Deprecated.
                'type' => true,
                'typemustmatch' => true,
                'usemap' => true,
                'width' => true,
            )
        ),
        'param'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'name'      => true,
                'type'      => true, // Deprecated.
                'value'     => true,
                'valuetype' => true, // Deprecated.
            )
        ),
        'picture' => nonaki_prefix_allowed_global_attributes(),
        'source'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'media'  => true,
                'sizes'  => true,
                'src'    => true,
                'srcset' => true,
                'type'   => true,
            )
        ),

        // Scripting.
        'canvas'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'height' => true,
                'width'  => true,
            )
        ),
        'noscript' => nonaki_prefix_allowed_global_attributes(),
        'script'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'async'          => true,
                'crossorigin'    => true,
                'defer'          => true,
                'integrity'      => true,
                'language'       => true, // Deprecated.
                'nomodule'       => true,
                'referrerPolicy' => true,
                'src'            => true,
                'text'           => true,
                'type'           => true,
                'type.module'    => true,
            )
        ),

        // Demarcating edits.
        'del' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'cite'     => true,
                'datetime' => true,
            )
        ),
        'ins' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'cite'     => true,
                'datetime' => true,
            )
        ),

        // Table Content.
        'caption'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align' => true, // Deprecated.
            )
        ),
        'col'      => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'span'    => true,
                'valign'  => true, // Deprecated.
                'width'   => true, // Deprecated.
            )
        ),
        'colgroup'      => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'span'    => true,
                'valign'  => true, // Deprecated.
                'width'   => true, // Deprecated.
            )
        ),
        'table'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'       => true, // Deprecated.
                'bgcolor'     => true, // Deprecated.
                'border'      => true, // Deprecated.
                'cellpadding' => true, // Deprecated.
                'cellspacing' => true, // Deprecated.
                'frame'       => true, // Deprecated.
                'rules'       => true, // Deprecated.
                'summary'     => true, // Deprecated.
                'width'       => true, // Deprecated.
            )
        ),
        'tbody'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'valign'  => true, // Deprecated.
            )
        ),
        'td'       => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'abbr'    => true, // Deprecated.
                'align'   => true, // Deprecated.
                'axis'    => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'colspan' => true,
                'headers' => true,
                'rowspan' => true,
                'scope'   => true, // Deprecated.
                'valign'  => true, // Deprecated.
                'width'   => true, // Deprecated.
            )
        ),
        'tfoot'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'valign'  => true, // Deprecated.
            )
        ),
        'th'       => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'abbr'    => true,
                'align'   => true, // Deprecated.
                'axis'    => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'colspan' => true,
                'headers' => true,
                'rowspan' => true,
                'scope'   => true,
                'valign'  => true, // Deprecated.
                'width'   => true, // Deprecated.
            )
        ),
        'thead'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'valign'  => true, // Deprecated.
            )
        ),
        'tr'       => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'align'   => true, // Deprecated.
                'bgcolor' => true, // Deprecated.
                'char'    => true, // Deprecated.
                'charoff' => true, // Deprecated.
                'valign'  => true, // Deprecated.
            )
        ),

        // Forms.
        'button'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'autofocus'      => true,
                'disabled'       => true,
                'form'           => true,
                'formaction'     => true,
                'formenctype'    => true,
                'formmethod'     => true,
                'formnovalidate' => true,
                'formtarget'     => true,
                'name'           => true,
                'type'           => true,
                'value'          => true,
            )
        ),
        'datalist' => nonaki_prefix_allowed_global_attributes(),
        'fieldset' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'disabled' => true,
                'form'     => true,
                'name'     => true,
            )
        ),
        'form'     => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'accept'         => true, // Deprecated.
                'accept-charset' => true,
                'action'         => true,
                'enctype'        => true,
                'method'         => true,
                'name'           => true,
                'novalidate'     => true,
                'target'         => true,
            )
        ),
        'input'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'accept'         => true,
                'alt'            => true,
                'autocomplete'   => true,
                'autofocus'      => true,
                'capture'        => true,
                'checked'        => true,
                'dirname'        => true,
                'disabled'       => true,
                'form'           => true,
                'formaction'     => true,
                'formenctype'    => true,
                'formmethod'     => true,
                'formnovalidate' => true,
                'formtarget'     => true,
                'height'         => true,
                'list'           => true,
                'max'            => true,
                'maxlength'      => true,
                'min'            => true,
                'minlength'      => true,
                'multiple'       => true,
                'name'           => true,
                'pattern'        => true,
                'placeholder'    => true,
                'readonly'       => true,
                'required'       => true,
                'size'           => true,
                'src'            => true,
                'step'           => true,
                'type'           => true,
                'value'          => true,
                'width'          => true,
            )
        ),
        'label'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'for'  => true,
                'form' => true, // Deprecated.
            )
        ),
        'legend'   => nonaki_prefix_allowed_global_attributes(),
        'meter'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'form'    => true,
                'high'    => true,
                'low'     => true,
                'max'     => true,
                'min'     => true,
                'optimum' => true,
                'value'   => true,
            )
        ),
        'optgroup' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'disabled' => true,
                'label' => true,
            )
        ),
        'option'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'disabled' => true,
                'label'    => true,
                'selected' => true,
                'value'    => true,
            )
        ),
        'output'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'for' => true,
            )
        ),
        'progress' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'max'   => true,
                'value' => true,
            )
        ),
        'select'   => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'autofocus' => true,
                'disabled'  => true,
                'form'      => true,
                'multiple'  => true,
                'name'      => true,
                'required'  => true,
                'size'      => true,
            )
        ),
        'textarea' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'autofocus'   => true,
                'cols'        => true,
                'disabled'    => true,
                'form'        => true,
                'maxlength'   => true,
                'minlength'   => true,
                'name'        => true,
                'placeholder' => true,
                'readonly'    => true,
                'required'    => true,
                'rows'        => true,
                'spellcheck'  => true,
                'wrap'        => true,
            )
        ),

        // Interactive Elements.
        'details' => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'open' => true,
            )
        ),
        'dialog'  => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'open' => true,
            )
        ),
        'menu'    => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'label' => true,
                'type' => true,
            )
        ),
        'summary' => nonaki_prefix_allowed_global_attributes(),

        // Web Components.
        'slot'     => array_merge(
            nonaki_prefix_allowed_global_attributes(),
            array(
                'name' => true,
            )
        ),
        'template' => nonaki_prefix_allowed_global_attributes(),
        // mjml
        'mjml' => nonaki_mjml_allowed_attributes(),
        'mj-mjml' => nonaki_mjml_allowed_attributes(),
        'mj-head' => nonaki_mjml_allowed_attributes(),
        'mj-body' => nonaki_mjml_allowed_attributes(),
        'mj-wrapper' => nonaki_mjml_allowed_attributes(),
        'mj-wrapper' => nonaki_mjml_allowed_attributes(),
        'mj-group' => nonaki_mjml_allowed_attributes(),
        'mj-section' => nonaki_mjml_allowed_attributes(),
        'mj-column' => nonaki_mjml_allowed_attributes(),
        'mj-text' => nonaki_mjml_allowed_attributes(),
        'mj-image' => nonaki_mjml_allowed_attributes(),
        'mj-button' => nonaki_mjml_allowed_attributes(),
        'mj-social' => nonaki_mjml_allowed_attributes(),
        'mj-social-element' => nonaki_mjml_allowed_attributes(),
        'mj-divider' => nonaki_mjml_allowed_attributes(),
        'mj-spacer' => nonaki_mjml_allowed_attributes(),
        'mj-style' => nonaki_mjml_allowed_attributes(),
        'mj-font' => nonaki_mjml_allowed_attributes(),
        'mj-hero' => nonaki_mjml_allowed_attributes(),
        'mj-navbar' => nonaki_mjml_allowed_attributes(),
        'mj-navbar-link' => nonaki_mjml_allowed_attributes(),
        'mj-raw' => nonaki_mjml_allowed_attributes(),

    );
}

function nonaki_mjml_allowed_attributes()
{
    return array();
}

/**
 * Allowed Global Attributes.
 *
 * @return array
 */
function nonaki_prefix_allowed_global_attributes()
{
    return array(
        'aria-*'              => true,
        'accesskey'           => true,
        'autocapitalize'      => true,
        'autocomplete'        => true,
        'class'               => true,
        'contenteditable'     => true,
        'data-*'              => true,
        'dir'                 => true,
        'draggable'           => true,
        'dropzone'            => true,
        'exportparts'         => true,
        'hidden'              => true,
        'id'                  => true,
        'inputmode'           => true,
        'is'                  => true,
        'itemid'              => true,
        'intemprop'           => true,
        'itemref'             => true,
        'itemscope'           => true,
        'itemtype'            => true,
        'lang'                => true,
        'part'                => true,
        'slot'                => true,
        'spellcheck'          => true,
        'style'               => true,
        'tabindex'            => true,
        'title'               => true,
        'translate'           => true,
        'onabort'             => true,
        'onautocomplete'      => true,
        'onautocompleteerror' => true,
        'onblur'              => true,
        'oncancel'            => true,
        'oncanplay'           => true,
        'oncanplaythrough'    => true,
        'onchange'            => true,
        'onclick'             => true,
        'onclose'             => true,
        'oncontextmenu'       => true,
        'oncuechange'         => true,
        'ondblclick'          => true,
        'ondrag'              => true,
        'ondragend'           => true,
        'ondragenter'         => true,
        'ondragexit'          => true,
        'ondragleave'         => true,
        'ondragover'          => true,
        'ondragstart'         => true,
        'ondrop'              => true,
        'ondurationchange'    => true,
        'onemptied'           => true,
        'onended'             => true,
        'onerror'             => true,
        'onfocus'             => true,
        'oninput'             => true,
        'oninvalid'           => true,
        'onkeydown'           => true,
        'onkeypress'          => true,
        'onkeyup'             => true,
        'onload'              => true,
        'onloadeddata'        => true,
        'onloadedmetadata'    => true,
        'onloadstart'         => true,
        'onmousedown'         => true,
        'onmouseenter'        => true,
        'onmouseleave'        => true,
        'onmousemove'         => true,
        'onmouseout'          => true,
        'onmouseover'         => true,
        'onmouseup'           => true,
        'onmousewheel'        => true,
        'onpause'             => true,
        'onplay'              => true,
        'onplaying'           => true,
        'onprogress'          => true,
        'onratechange'        => true,
        'onreset'             => true,
        'onresize'            => true,
        'onscroll'            => true,
        'onseeked'            => true,
        'onseeking'           => true,
        'onselect'            => true,
        'onshow'              => true,
        'onsort'              => true,
        'onstalled'           => true,
        'onsubmit'            => true,
        'onsuspend'           => true,
        'ontimeupdate'        => true,
        'ontoggle'            => true,
        'onvolumechange'      => true,
        'onwaiting'           => true,
    );
}

if (!function_exists('nonaki_e')) {
    function nonaki_e($content)
    {
        return $content;
        // return wp_kses($content, nonaki_prefix_allowed_tags_all());
    }
}

if (!function_exists('nonaki_get_presets')) {
    function nonaki_get_presets()
    {
        $data = [];
        $presets = apply_filters('nonaki_email_presets', $data);
        return $presets;
    }
}

if (!function_exists('nonaki_get_preset')) {
    function nonaki_get_preset($preset_id)
    {
        if (isset(nonaki_get_presets()[$preset_id])) {
            return nonaki_get_presets()[$preset_id];
        }
        return null;
    }
}

if (!function_exists('is_nonaki_pro_avilable')) {
    function is_nonaki_pro_avilable()
    {
        return false;
    }
}

if (!function_exists('nonaki_export_to_csv')) {
    function nonaki_export_to_csv($post_type, $meta_keys)
    {

        // Set the post type
        // $post_type = 'nonaki_contact';

        // Set the meta keys to export


        // Set the file name
        $filename = 'contact-export-' . date('Y-m-d') . '.csv';

        // Set the headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        // Open the file pointer
        $f = fopen('php://output', 'w');

        // Write the headers to the CSV file
        fputcsv($f, array(
            'First Name',
            'Last Name',
            'Subscription Status',
            'Contact Source',
        ));

        // Query the posts
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // Loop through the posts
            while ($query->have_posts()) {
                $query->the_post();

                // Get the meta data
                $meta = get_post_meta(get_the_ID());

                // Create an array to hold the post data
                $data = array();

                // Add the meta data to the array
                foreach ($meta_keys as $key) {
                    $data[] = isset($meta[$key][0]) ? $meta[$key][0] : '';
                }

                // Write the data to the CSV file
                fputcsv($f, $data);
            }

            // Reset post data
            wp_reset_postdata();
        }

        // Close the file pointer
        fclose($f);

        // Stop the script
        exit;
    }
}

if (!function_exists('download_and_activate_plugin')) {
    function download_and_activate_plugin($plugin_url, $plugin_folder, $plugin_file_name)
    {
        // Get the WordPress plugin installation path
        $plugins_dir = WP_PLUGIN_DIR;

        // Set the path for the downloaded plugin file
        $plugin_file = $plugins_dir . '/' . basename($plugin_url);

        // Download the plugin file
        $response = wp_remote_get($plugin_url);

        if (is_wp_error($response)) {
            // Plugin download failed
            return false;
        }

        // Save the downloaded plugin file
        $body = wp_remote_retrieve_body($response);
        $saved = file_put_contents($plugin_file, $body);

        if ($saved === false) {
            // Unable to save the plugin file
            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($plugin_file);
        if ($res === true) {
            $zip->extractTo($plugins_dir);
            $zip->close();
        } else {
        }

        // Activate the plugin
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $activate = activate_plugin($plugin_folder . '/' . $plugin_file_name);

        if (is_wp_error($activate)) {
            // Plugin activation failed
            return false;
        }

        unlink($plugin_file);
        return true;
    }
}

if (!function_exists('nonaki_deactivate_addon')) {
    function nonaki_deactivate_addon($plugin_folder, $plugin_file)
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active($plugin_folder . '/' . $plugin_file)) {
            deactivate_plugins($plugin_folder . '/' . $plugin_file);
            return true;
        }

        return false;
    }
}
