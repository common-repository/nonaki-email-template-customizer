<?php

namespace Nonaki;


class Cpt extends Base
{
    use BaseTrait;

    public function init()
    {

        add_action('init',                                                [$this, 'nonaki_email_template'], 0);
        add_filter("manage_{$this->get_post_type()}_posts_columns",       [$this, 'edit_columns']);
        add_action("manage_{$this->get_post_type()}_posts_custom_column", [$this, 'add_columns_data'], 10, 2);
        add_filter('post_row_actions',                                    [$this, 'nonaki_post_row_actions'], 10, 2);

        add_filter('post_row_actions',                                    [$this, 'remove_quick_edit'], 10, 1);
        add_filter('bulk_actions-edit-nonaki',                            [$this, 'bulk_actions']);
        add_action('admin_head',                                          [$this, 'cpt_css']);
    }



    public function bulk_actions($bulk_array)
    {
        unset($bulk_array['edit']);
        return $bulk_array;
    }


    function remove_quick_edit($actions)
    {
        unset($actions['inline hide-if-no-js']);
        return $actions;
    }

    public function nonaki_post_row_actions($actions, $post)
    {
        if ($post->post_type == 'nonaki') {
            $preivew_url = admin_url('index.php?page=nonaki&id=' . $post->ID);
            $actions['preivew'] = '<a href="' . esc_url($preivew_url) . '&mood=preview"  target="__blank" rel="permalink">Preview</a>';
        }
        return $actions;
    }

    public function edit_columns($columns)
    {
        unset($columns['date']);
        $columns['title']     = esc_html__('Template Name', 'nonaki');
        $columns['type']      = esc_html__('Type', 'nonaki');
        $columns['status']    = esc_html__('Status', 'nonaki');
        $columns['shortcode'] = esc_html__('Shortcode', 'nonaki');
        $columns['date']      = esc_html__('Date', 'nonaki');
        return $columns;
    }

    public function add_columns_data($column, $post_id)
    {
        $type     = get_post_meta($post_id, 'template_type', true);
        $sub_type = get_post_meta($post_id, 'template_sub_type', true);
        $status   = get_post_meta($post_id, 'nonaki_status', true);

        switch ($column) {
            case 'type':

                $st = (!empty(nonaki_get_template_sub_types($type)[$sub_type])) ? ' (' . nonaki_get_template_sub_types($type)[$sub_type] . ')' : '';
                $n_short_name = nonaki_get_short_name($type) . $st;
                esc_html_e($n_short_name, "nonaki");
                break;

            case 'status':

                if ($status) {
                    echo (Render::status_text(strtoupper($status), $status));
                } else {
                    echo (Render::status_text(strtoupper('INACTIVE'), 'inactive'));
                }

                break;

            case 'shortcode':

                if ($type === 'general') {
                    echo '<code class="nonaki-general-column">[nonaki id="' . esc_attr($post_id) . '"]</code>';
                } else {
                    echo 'N/A';
                }

                break;
        }
    }

    public function nonaki_email_template()
    {

        $labels = array(
            'name'                  => esc_html__('Templates', 'nonaki'),
            'singular_name'         => esc_html__('Template', 'nonaki'),
            'menu_name'             => esc_html__('Templates', 'nonaki'),
            'name_admin_bar'        => esc_html__('Templates', 'nonaki'),
            'archives'              => esc_html__('Templates Archives', 'nonaki'),
            'attributes'            => esc_html__('Template Attributes', 'nonaki'),
            'parent_item_colon'     => esc_html__('Parent Item:', 'nonaki'),
            'all_items'             => esc_html__('Templates', 'nonaki'),
            'add_new_item'          => esc_html__('Create New Template', 'nonaki'),
            'add_new'               => esc_html__('Create New Template', 'nonaki'),
            'new_item'              => esc_html__('New Template', 'nonaki'),
            'edit_item'             => esc_html__('Edit Template', 'nonaki'),
            'update_item'           => esc_html__('Update Item', 'nonaki'),
            'view_item'             => esc_html__('View Template', 'nonaki'),
            'view_items'            => esc_html__('View Templates', 'nonaki'),
            'search_items'          => esc_html__('Search Template', 'nonaki'),
            'not_found'             => esc_html__('Template Not found', 'nonaki'),
            'not_found_in_trash'    => esc_html__('Not found in Trash', 'nonaki'),
            'featured_image'        => esc_html__('Template Image', 'nonaki'),
            'set_featured_image'    => esc_html__('Set Template image', 'nonaki'),
            'remove_featured_image' => esc_html__('Remove Template image', 'nonaki'),
            'use_featured_image'    => esc_html__('Use as Template image', 'nonaki'),
            'insert_into_item'      => esc_html__('Insert into Template', 'nonaki'),
            'uploaded_to_this_item' => esc_html__('Uploaded to this Template', 'nonaki'),
            'items_list'            => esc_html__('Items list', 'nonaki'),
            'items_list_navigation' => esc_html__('Items list navigation', 'nonaki'),
            'filter_items_list'     => esc_html__('Filter items list', 'nonaki'),
        );
        $args = array(
            'label'                 => esc_html__('Email Template', 'nonaki'),
            'description'           => esc_html__('Email template builder', 'nonaki'),
            'labels'                => $labels,
            'supports'              => array('title'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
        );
        register_post_type($this->get_post_type(), $args);
    }

    public function cpt_css()
    {
?>
        <style>
            .column-status {
                width: 80px;
            }

            .nonaki-general-column {
                border-radius: 5px;
                padding: 5px
            }
        </style>
<?php
    }
}
