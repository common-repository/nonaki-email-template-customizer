<?php
defined('ABSPATH') || exit;

if (!is_user_logged_in() && !is_admin()) {
    die("Ops you are not allowed!");
}

$nonaki_content  = '';
$nonaki_title    = 'Editor';
$nonaki_post_id  = 'new';
$nonaki_exit_url = NONAKI_ADMIN_DASHBOARD_URL;
$nonaki_type     = null;
$nonaki_sub_type = null;
$nonaki_content_type = 'mail';
$nonaki_compiled_content = '';
$nonaki_nonaki_exit_url = admin_url('/edit.php?post_type=nonaki');

if (isset($_GET['preset'])) {
    $nonaki_preset = nonaki_get_preset(sanitize_text_field($_GET['preset']));
    if ($nonaki_preset) {
        $nonaki_content = $nonaki_preset['content'];
        $nonaki_nonaki_exit_url = admin_url('/admin.php?page=nonaki-choice-type'); // should be save url
    }
}

if (isset($_GET['id'])) {

    $nonaki_post_id        = sanitize_text_field($_GET['id']);

    if (get_post_status($nonaki_post_id)) {
        $nonaki_postid              = $nonaki_post_id; //This is page id or post id
        $nonaki_content_post        = get_post($nonaki_postid);
        $nonaki_content             = $nonaki_content_post->post_content;
        $nonaki_compiled_content    = get_post_meta($nonaki_post_id, 'compiled_content', true);
        $nonaki_content_type        = get_post_meta($nonaki_post_id, 'content_type', true);
        $nonaki_title               = $nonaki_content_post->post_title;
        $nonaki_nonaki_exit_url     = admin_url("/post.php?post=" . $nonaki_post_id . "&action=edit");
        $nonaki_type                = get_post_meta($nonaki_post_id, 'template_type', true);
        $nonaki_sub_type            = get_post_meta($nonaki_post_id, 'template_sub_type', true);
    }
}

$favicon_url = NONAKI_URL   . "assets/images/icon.svg";
$editor_js   = NONAKI_URL   . "assets/js/editor.js";
$editor_css  = NONAKI_URL   . "assets/css/style.css";

if (isset($_GET['mood']) && $_GET['mood'] === 'preview') {

    $nonaki_template_editing_url = admin_url('index.php?page=nonaki&id=' . $nonaki_post_id);
    include_once 'preview.php';
    exit;
}

// If developer mode is on then add development js
if (defined('NONAKI_DEV')) {
    $editor_js = 'http://localhost:3000/main.js';
}
$nonaki_editor_mode = $nonaki_content_type;

$nonaki_data = json_encode([
    'editorMode' => $nonaki_editor_mode
]);
