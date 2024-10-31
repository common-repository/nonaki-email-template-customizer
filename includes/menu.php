<?php

namespace Nonaki;

class Menu
{
    use BaseTrait;
    private $parent_slug = 'wpnonaki';

    public function __construct()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'nonaki-choice-type') {

            add_action('admin_head', [$this, 'choice_page_css']);
        }
    }
    public function init()
    {
        $parent_slug = $this->parent_slug;
        add_menu_page(
            'Nonaki',
            'Nonaki',
            'manage_options',
            $parent_slug,
            [$this, 'dashboard_page'],
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNi4wLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMTQuOSAyMTkuOSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjE0LjkgMjE5Ljk7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxnPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00My4yLDExNy4zbC0yNS40LTE2LjljLTIuNi0xLjgtNS41LTIuOS04LjYtMy41bC05LTEuOGwwLjgtNi4yYzEuOS0xNC4zLDExLjQtMjYuNCwyNC45LTMxLjZsMCwwbDE3LjMtNi42DQoJCVYxMTcuM3ogTTE1LjUsODQuOGMzLjQsMSw2LjYsMi42LDkuNiw0LjVsNC44LDMuMlY3MEMyMy4yLDcyLjgsMTgsNzguMiwxNS41LDg0Ljh6Ii8+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE3MS43LDExNy4zVjUwLjdsMTcuMyw2LjZjMTMuNSw1LjEsMjMuMSwxNy4yLDI0LjksMzEuNmwwLjksNi43bC02LjcsMC44Yy0zLjgsMC40LTcuNCwxLjctMTAuNSwzLjgNCgkJTDE3MS43LDExNy4zeiBNMTg1LDcwdjIyLjVsNS4yLTMuNWMyLjgtMS45LDUuOS0zLjMsOS4xLTQuNEMxOTYuOCw3OC4xLDE5MS42LDcyLjgsMTg1LDcweiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik03Mi42LDEzNi45bC00Mi44LTI4LjRWMjAuOGMwLTExLDktMjAsMjAtMjBoMTE1YzExLDAsMjAsOSwyMCwyMHY4Ny44bC00Mi44LDI4LjRsLTIxLjktNy4zDQoJCWMtOC4zLTMtMTcuNS0zLTI1LjksMGwtMC4xLDBMNzIuNiwxMzYuOXogTTEyNC44LDExN2wxNS41LDUuMmwzMS40LTIwLjhWMjAuOGMwLTMuNy0zLTYuNy02LjctNi43aC0xMTVjLTMuNywwLTYuNywzLTYuNyw2Ljd2ODAuNg0KCQlsMzEuNCwyMC45bDE1LjUtNS4yQzEwMS4zLDExMywxMTMuNiwxMTMsMTI0LjgsMTE3eiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yMTQuMywxNjUuN2wtMTAuMS02LjFjLTQuNy0yLjgtOS43LTUuMi0xNC45LTYuOWwtNjMuMS0yMS4xbDY0LTQyLjRjNC45LTMuMiwxMC42LTUuMywxNi40LTZsNi41LTAuNw0KCQlsMC44LDYuNWMwLjIsMS43LDAuMywzLjQsMC4zLDUuMVYxNjUuN3ogTTE1Ni4zLDEyNy42bDM3LjIsMTIuNWMyLjUsMC44LDUsMS44LDcuNSwyLjlWOTguM2MtMS4yLDAuNS0yLjMsMS4yLTMuNCwxLjlMMTU2LjMsMTI3LjYNCgkJeiIvPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0wLjYsMTY1LjdWOTMuNWMwLTEuNiwwLjEtMy4yLDAuMy00LjdsMS03bDkuOCwyYzQuOCwwLjksOS4zLDIuOCwxMy40LDUuNWw2My41LDQyLjJsLTYzLjEsMjEuMQ0KCQljLTUuMiwxLjctMTAuMiw0LjEtMTQuOSw2LjlMMC42LDE2NS43eiBNMTMuOSw5OC4zdjQ0LjZjMi40LTEuMSw0LjktMiw3LjQtMi45bDM3LjMtMTIuNWwtNDAuOS0yNy4yDQoJCUMxNi41LDk5LjYsMTUuMiw5OC45LDEzLjksOTguM3oiLz4NCgk8Zz4NCgkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTExOS45LDM2LjVINTguN2MtMS44LDAtMy4yLTEuNC0zLjItMy4ydi0zLjhjMC0xLjgsMS40LTMuMiwzLjItMy4yaDYxLjJjMS44LDAsMy4yLDEuNCwzLjIsMy4ydjMuOA0KCQkJQzEyMy4xLDM1LjEsMTIxLjcsMzYuNSwxMTkuOSwzNi41eiIvPg0KCQk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTU1LDUzSDU4LjdjLTEuOCwwLTMuMi0xLjQtMy4yLTMuMlY0NmMwLTEuOCwxLjQtMy4yLDMuMi0zLjJIMTU1YzEuOCwwLDMuMiwxLjQsMy4yLDMuMnYzLjgNCgkJCUMxNTguMiw1MS42LDE1Ni43LDUzLDE1NSw1M3oiLz4NCgkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTgxLjgsNjkuNUg1OC43Yy0xLjgsMC0zLjItMS40LTMuMi0zLjJ2LTMuOGMwLTEuOCwxLjQtMy4yLDMuMi0zLjJoMjMuMWMxLjgsMCwzLjIsMS40LDMuMiwzLjJ2My44DQoJCQlDODUsNjgsODMuNiw2OS41LDgxLjgsNjkuNXoiLz4NCgkJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE1NSw4NS45SDU4LjdjLTEuOCwwLTMuMi0xLjQtMy4yLTMuMlY3OWMwLTEuOCwxLjQtMy4yLDMuMi0zLjJIMTU1YzEuOCwwLDMuMiwxLjQsMy4yLDMuMnYzLjgNCgkJCUMxNTguMiw4NC41LDE1Ni43LDg1LjksMTU1LDg1Ljl6Ii8+DQoJCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xMTUsMTAyLjRINTguN2MtMS44LDAtMy4yLTEuNC0zLjItMy4ydi0zLjhjMC0xLjgsMS40LTMuMiwzLjItMy4ySDExNWMxLjgsMCwzLjIsMS40LDMuMiwzLjJ2My44DQoJCQlDMTE4LjIsMTAxLDExNi44LDEwMi40LDExNSwxMDIuNHoiLz4NCgk8L2c+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE3NS4xLDIxOS4ySDM5LjdjLTEwLjUsMC0yMC4zLTQuMS0yNy43LTExLjVjLTAuNS0wLjUtMS0xLTEuNS0xLjZjMCwwLTAuOS0xLTEuOC0yLjINCgkJYy0wLjktMS4yLTEuNy0yLjMtMS43LTIuM2MtMS45LTIuOS0zLjMtNS45LTQuNC05Yy0xLjQtNC0yLjEtOC4yLTIuMS0xMi41di0yOS45bDMuMi0xLjljNS41LTMuMywxMS40LTYuMSwxNy42LTguMWw2OC43LTIzDQoJCWMxMS4yLTQsMjMuNS00LDM0LjcsMGw2OC43LDIzYzYuMSwyLjEsMTIuMSw0LjgsMTcuNiw4LjFsMy4yLDEuOVYxODBDMjE0LjMsMjAxLjYsMTk2LjcsMjE5LjIsMTc1LjEsMjE5LjJ6IE0xOC44LDE5NS4xbDAuMywwLjQNCgkJYzAuNywxLDEuNSwxLjksMi40LDIuOGM0LjksNC45LDExLjQsNy42LDE4LjMsNy42aDEzNS4zYzE0LjMsMCwyNS45LTExLjYsMjUuOS0yNS45di0yMi4zYy0zLjctMi03LjYtMy43LTExLjctNS4xbC02OC45LTIzLjENCgkJYy04LjMtMy0xNy41LTMtMjUuOSwwbC0wLjEsMGwtNjguOCwyMy4xYy00LDEuNC03LjksMy4xLTExLjcsNS4xVjE4MGMwLDIuOCwwLjUsNS42LDEuNCw4LjNjMC43LDIuMiwxLjgsNC4zLDMuMSw2LjINCgkJQzE4LjQsMTk0LjcsMTguNiwxOTQuOSwxOC44LDE5NS4xeiIvPg0KPC9nPg0KPC9zdmc+DQo=',
            3
        );

        $submenu_pages = array(
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'Dashboard',
                'menu_title'  => 'Dashboard',
                'capability'  => 'manage_options',
                'menu_slug'   => $parent_slug,
                'function'    => [$this, 'dashboard_page'], // Uses the same callback function as parent menu.
            ),
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'New Template',
                'menu_title'  => 'New Template',
                'capability'  => 'manage_options',
                'menu_slug'   => 'nonaki-choice-type',
                'function'    => [$this, 'choice_page'], // Uses the same callback function as parent menu.
            ),
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'Templates',
                'menu_title'  => 'Templates',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=nonaki',
                'function'    => null, // Uses the same callback function as parent menu.
            ),
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'Contacts',
                'menu_title'  => 'Contacts',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit.php?post_type=nonaki_contact',
                'function'    => null, // Uses the same callback function as parent menu.
            ),
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'List',
                'menu_title'  => 'List',
                'capability'  => 'manage_options',
                'menu_slug'   => 'edit-tags.php?taxonomy=list&post_type=nonaki_contact',
                'function'    => null, // Uses the same callback function as parent menu.
            ),
            array(
                'parent_slug' =>  $parent_slug,
                'page_title'  => 'Add-ons',
                'menu_title'  => 'Add-ons',
                'capability'  => 'manage_options',
                'menu_slug'   => 'nonaki-addons',
                'function'    => [$this, 'addons_page'], // Uses the same callback function as parent menu.
            ),
            // array(
            //     'parent_slug' =>  $parent_slug,
            //     'page_title'  => 'Campaign',
            //     'menu_title'  => 'Campaign',
            //     'capability'  => 'manage_options',
            //     'menu_slug'   => 'edit.php?post_type=nonaki_campaign',
            //     'function'    => null, // Uses the same callback function as parent menu.
            // ),
        );


        $nonaki_submenu_pages = apply_filters('nonaki_submenu_pages', $submenu_pages);
        # Add each submenu item to custom admin menu.
        foreach ($nonaki_submenu_pages as $submenu) {

            add_submenu_page(
                $submenu['parent_slug'],
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']
            );
        }

        add_submenu_page(
            $parent_slug,
            esc_html__('Settings', 'nonaki'),
            esc_html__('Settings', 'nonaki'),
            'manage_options',
            'nonaki-settings',
            [
                \Nonaki\Settings::get_instance(),
                'settings_page'
            ],
            8
        );

        add_filter('parent_file', [$this, 'mbe_set_current_menu']);
    }

    public function mbe_set_current_menu($parent_file)
    {
        global $submenu_file, $current_screen, $pagenow;

        # Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
        // Templates

        if ($current_screen->post_type == 'nonaki') {


            if ($pagenow == 'edit.php') {
                $submenu_file = 'edit.php?post_type=nonaki';
            }

            if ($pagenow == 'post.php') {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }

            $parent_file = $this->parent_slug;
        }

        // Contacts

        if ($current_screen->post_type == 'nonaki_contact') {

            if ($pagenow == 'post.php') {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }
            if ($pagenow == 'edit.php') {
                $submenu_file = 'edit.php?post_type=nonaki_contact';
            }

            if ($pagenow == 'edit-tags.php') {
                $submenu_file = 'edit-tags.php?taxonomy=list&post_type=nonaki_contact';
            }

            $parent_file = $this->parent_slug;
        }

        // campaign
        if ($current_screen->post_type == 'nonaki_campaign') {

            if ($pagenow == 'post.php') {
                $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            }
            if ($pagenow == 'edit.php') {
                $submenu_file = 'edit.php?post_type=nonaki_campaign';
            }

            if ($pagenow == 'edit-tags.php') {
                $submenu_file = 'edit-tags.php?taxonomy=list&post_type=nonaki_campaign';
            }

            $parent_file = $this->parent_slug;
        }

        return $parent_file;
    }

    public function menu_content()
    {
        $this->redirect_to_nonaki_builder();
    }

    public function redirect_to_nonaki_builder()
    {
        wp_safe_redirect(add_query_arg(array('page' => 'nonaki'), admin_url('index.php')));
        exit;
    }

    public function choice_page()
    {
        include_once(NONAKI_DIR . '/pages/new-template.php');
    }

    public function dashboard_page()
    {
        include_once(NONAKI_DIR . '/pages/dashboard.php');
    }

    public function addons_page()
    {
        include_once(NONAKI_DIR . '/pages/add-ons.php');
    }

    public function choice_page_css()
    {
?>
        <!-- <style>
            .mx-5 {
                margin-left: 1.25rem;
                margin-right: 1.25rem;
            }

            .mr-5 {
                margin-right: 1.25rem;
            }

            .flex {
                display: flex;
            }

            .h-6 {
                height: 1.5rem;
            }

            .h-screen {
                height: 100vh;
            }

            .w-10 {
                width: 2.5rem;
            }

            .w-20 {
                width: 5rem;
            }

            .w-6 {
                width: 1.5rem;
            }

            .w-full {
                width: 100%;
            }

            .transform {
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .flex-col {
                flex-direction: column;
            }

            .items-center {
                align-items: center;
            }

            .justify-start {
                justify-content: flex-start;
            }

            .justify-center {
                justify-content: center;
            }

            .justify-between {
                justify-content: space-between;
            }

            .gap-2 {
                gap: 0.5rem;
            }

            .gap-5 {
                gap: 1.25rem;
            }

            .rounded-lg {
                border-radius: 0.5rem;
            }

            .rounded-md {
                border-radius: 0.375rem;
            }

            .bg-\[\#F4F8FC\] {
                --tw-bg-opacity: 1;
                background-color: rgb(244 248 252 / var(--tw-bg-opacity));
            }

            .bg-blue-50 {
                --tw-bg-opacity: 1;
                background-color: rgb(239 246 255 / var(--tw-bg-opacity));
            }

            .bg-green-500 {
                --tw-bg-opacity: 1;
                background-color: rgb(34 197 94 / var(--tw-bg-opacity));
            }

            .bg-white {
                --tw-bg-opacity: 1;
                background-color: rgb(255 255 255 / var(--tw-bg-opacity));
            }

            .p-3 {
                padding: 0.75rem;
            }

            .px-10 {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
            }

            .px-14 {
                padding-left: 3.5rem;
                padding-right: 3.5rem;
            }

            .px-2 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .px-5 {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }

            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .py-3 {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .py-5 {
                padding-top: 1.25rem;
                padding-bottom: 1.25rem;
            }

            .text-lg {
                font-size: 1.125rem;
                line-height: 1.75rem;
            }

            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }

            .text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }

            .font-light {
                font-weight: 300;
            }

            .font-semibold {
                font-weight: 600;
            }

            .text-white {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity));
            }

            .shadow {
                --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            }

            .shadow-md {
                --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            }

            .hover\:shadow-lg:hover {
                --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
                --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            }




            .nonaki_btn_create {
                text-decoration: none;
            }

            #wpcontent {
                padding-left: 0px !important;
                background-color: #F4F8FC;
            }

            #nonaki_new_div {
                width: 100%;
                height: 50vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            #nonaki_new_div>h1 {
                margin-bottom: 30px;
                color: #1D2327;
            }

            .nonaki_options {
                display: flex;
            }

            .nonaki_option {
                width: 300px;
                border: 1px solid gray;
                border-radius: 5px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 10px;
                margin: 5px;
                text-decoration: none;
                color: #9AA6B9;
                background: #242A3B;
                transition: 0.2s all;
            }

            .nonaki_option:hover {
                background: #1D2327;
                box-shadow: 0 10px 10px rgb(0 0 0 / 25%);

            }

            .nonaki_option>h3 {
                color: #9AA6B9;
            }

            .nonaki_option>svg {
                width: 100px;
                color: #9AA6B9;
            }
        </style> -->
<?php
    }
}
