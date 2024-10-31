<?php

defined('ABSPATH') || exit;
$it = [
    [
        'id' => 'cf7',
        'type'        => 'Form',
        'title'       => 'Contact form 7',
        'description' => 'Design your contact form 7 email templates',
        'icon'        => 'https://plugins.svn.wordpress.org/contact-form-7/assets/icon-128x128.png',
        'download_link'  => 'https://github.com/prappo/nonaki-cf7/archive/refs/heads/main.zip',
        'is_pro' => false,
        'class' => '\Nonaki_Addon\Cf7',
        'folder_name' => 'nonaki-cf7-main',
        'file_name' => 'nonaki-cf7.php',
        'avilable' => true,
    ],
    [
        'id' => 'ninja',
        'type'        => 'Form',
        'title'       => 'Ninja Forms',
        'description' => 'Design your Ninja Forms email templates',
        'icon'        => 'https://plugins.svn.wordpress.org/ninja-forms/assets/icon-128x128.png',
        'download_link'  => 'https://github.com/prappo/nonaki-cf7/archive/refs/heads/main.zip',
        'is_pro' => false,
        'class' => '\NinjaForms',
        'folder_name' => 'nonaki-cf7-main',
        'file_name' => 'nonaki-cf7.php',
        'avilable' => false,
    ],
    [
        'id' => 'wpforms',
        'type'        => 'Form',
        'title'       => 'WPForms',
        'description' => 'Design your WPForms email templates',
        'icon'        => 'https://plugins.svn.wordpress.org/wpforms-lite/assets/icon-128x128.png',
        'download_link'  => 'https://github.com/prappo/nonaki-cf7/archive/refs/heads/main.zip',
        'is_pro' => true,
        'class' => '\NinjaForms',
        'folder_name' => 'nonaki-cf7-main',
        'file_name' => 'nonaki-cf7.php',
        'avilable' => false,
    ],
    [
        'id' => 'fluentform',
        'type'        => 'Form',
        'title'       => 'Fluent Forms',
        'description' => 'Design your Fluent Forms email templates',
        'icon'        => 'https://plugins.svn.wordpress.org/fluentform/assets/icon-128x128.png',
        'download_link'  => 'https://github.com/prappo/nonaki-cf7/archive/refs/heads/main.zip',
        'is_pro' => true,
        'class' => '\NinjaForms',
        'folder_name' => 'nonaki-cf7-main',
        'file_name' => 'nonaki-cf7.php',
        'avilable' => false,
    ],
    [
        'id' => 'woocommerce',
        'type'        => 'Form',
        'title'       => 'WooCommerce',
        'description' => 'Design your WooCommerce email templates',
        'icon'        => 'https://plugins.svn.wordpress.org/woocommerce/assets/icon-128x128.gif',
        'download_link'  => 'https://github.com/prappo/nonaki-cf7/archive/refs/heads/main.zip',
        'is_pro' => true,
        'class' => '\NinjaForms',
        'folder_name' => 'nonaki-cf7-main',
        'file_name' => 'nonaki-cf7.php',
        'avilable' => false,
    ]
];


$nonaki_integrations = apply_filters('nonaki_integrations', $it);
if (isset($_GET['nii'])) {
    $plugin_details = null;

    foreach ($nonaki_integrations as $integration) {
        if ($_GET['nii'] === $integration['id']) {
            $plugin_details = $integration;
            break;
        }
    }

    if ($plugin_details) {
        $result = download_and_activate_plugin($plugin_details['download_link'], $plugin_details['folder_name'], $plugin_details['file_name']);
        if ($result) {
            wp_redirect(admin_url('/admin.php?page=nonaki-addons'));
            exit;
        }
    }
}

if (isset($_GET['nid'])) {
    $plugin_details = null;

    foreach ($nonaki_integrations as $integration) {
        if ($_GET['nid'] === $integration['id']) {
            $plugin_details = $integration;
            break;
        }
    }

    if ($plugin_details) {
        $result = nonaki_deactivate_addon($plugin_details['folder_name'], $plugin_details['file_name']);
        if ($result) {
            wp_redirect(admin_url('/admin.php?page=nonaki-addons'));
            exit;
        }
    }
}


?>

<div class="px-5">
    <div class="container m-auto py-10">
        <div class="grid grid-cols-4 gap-10">

            <?php foreach ($nonaki_integrations as $integrations) { ?>
                <div class="flex relative  justify-between flex-col gap-5 rounded-md bg-white px-10 py-10 shadow-sm hover:shadow-xl">

                    <?php if (!$integrations['avilable']) : ?>
                        <div class="w-full rounded-md h-full absolute top-0 left-0 flex justify-center items-center  bg-slate-300 bg-opacity-80">
                            <span class="absolute top-5 right-5 bg-yellow-200 rounded px-3 py-1">Coming soon...</span>
                        </div>
                    <?php endif; ?>

                    <img class="w-20" src="<?php echo esc_url($integrations['icon']); ?>" />
                    <div class="text-2xl font-semibold"><?php echo esc_html($integrations['title']); ?></div>
                    <div class=""><?php echo esc_html($integrations['description']); ?></div>
                    <div>
                        <?php if (!class_exists($integrations['class'])) : ?>
                            <a href="<?php echo esc_url(admin_url('/admin.php?page=nonaki-addons&nii=' . $integrations['id'])); ?>" type="button" class="rounded-lg bg-green-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Inistall & Active</a>
                        <?php else : ?>
                            <a href="<?php echo esc_url(admin_url('/admin.php?page=nonaki-addons&nid=' . $integrations['id'])); ?>" type="button" class="rounded-lg bg-red-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Deactivate</a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php } ?>
        </div>
    </div>
</div>