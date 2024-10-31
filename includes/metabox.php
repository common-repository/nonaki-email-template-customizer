<?php

namespace Nonaki;

class Metabox extends Base
{
    use BaseTrait;


    public function init()
    {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('admin_head', [$this, 'page_css']);
        add_action('admin_enqueue_scripts', [$this, 'codemirror_enqueue_scripts']);
        add_action('save_post', [$this, 'nonaki_save_postdata']);
    }



    public function add_script()
    {
        add_action('admin_footer', [$this, 'admin_script']);
        return $this;
    }



    public function add_type_metabox($post_type)
    {
        add_meta_box(
            'nonaki_template_type',
            esc_html__('Type', 'nonaki'),
            [$this, 'template_type'],
            $post_type,
            'side',
            'high',
        );

        return $this;
    }

    public function add_status_metabox($post_type)
    {
        $status = get_post_meta(get_the_ID(), 'nonaki_status', true);
        $status_icon = Render::status_icon($status);

        add_meta_box(
            'nonaki_template_status',
            "<div id='nonaki-status-box'>Status {$status_icon}</div>",
            [$this, 'template_type_status'],
            $post_type,
            'side',
            'high',
        );
        return $this;
    }

    public function add_source_code_metabox($post_type)
    {
        if (get_option('nonaki_enable_source_code')) {

            add_meta_box(
                'nonaki_email_template_code_metabox',
                esc_html__('Source Code', 'nonaki') . '<a class="button button-primary button-large nonakiBtnCopyCode" id="nonakiBtnCopyCode" href="#"> Copy Code</a>',
                [$this, 'source_code_metabox_content'],
                $post_type,
                'advanced',
                'high'
            );
        }
        return $this;
    }

    public function add_quick_preview_metabox($post_type)
    {
        add_meta_box(
            'nonaki_quick_preview_metabox',
            esc_html__('Template Preview', 'nonaki'),
            [$this, 'quick_preview_metabox_content'],
            $post_type,
            'advanced',
            'low'
        );



        return $this;
    }

    public function add_send_mail_metabox($post_type)
    {
        if (get_option('nonaki_enable_email_sending') &&  get_post_meta(get_the_ID(), 'template_type', true) == 'general') {
            if (get_current_screen()->action != 'add') {
                add_meta_box(
                    'nonaki_email_sender',
                    esc_html__('Send This Template', 'nonaki'),
                    [$this, 'send_mail'],
                    $post_type,
                    'side',
                    'low',
                );
            } else {
                add_action('edit_form_top', function () {
                    echo "Please publish template before editing";
                });
            }
        }
        return $this;
    }


    public function add_metabox($post_type)
    {

        if ($post_type == $this->get_post_type()) {

            $this->add_script()
                ->add_type_metabox($post_type)
                ->add_status_metabox($post_type)
                ->add_quick_preview_metabox($post_type)
                ->add_source_code_metabox($post_type)
                ->add_send_mail_metabox($post_type);
        }
    }

    public function nonaki_save_postdata($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (
            !isset($_POST['nonaki_template_type_nonce_field'])
            || !wp_verify_nonce(sanitize_text_field($_POST['nonaki_template_type_nonce_field']), 'nonaki_template_type_action')
        ) {
            return;
        }


        if (array_key_exists('nonaki_type', $_POST)) {
            update_post_meta(
                $post_id,
                'template_type',
                sanitize_text_field($_POST['nonaki_type'])
            );


            update_post_meta(
                $post_id,
                'content_type',
                nonaki_get_template_type_from_post_type(
                    sanitize_text_field($_POST['nonaki_type'])
                )
            );
        }

        if (array_key_exists('nonaki_sub_type', $_POST)) {
            update_post_meta(
                $post_id,
                'template_sub_type',
                sanitize_text_field($_POST['nonaki_sub_type'])
            );
        }

        if (array_key_exists('nonaki_status', $_POST)) {
            update_post_meta(
                $post_id,
                'nonaki_status',
                sanitize_text_field($_POST['nonaki_status'])
            );
        }
    }

    public function quick_preview_metabox_content($post)
    {
        $preview_link = admin_url('/index.php?page=nonaki&id=' . $post->ID . '&mood=preview');
        echo nonaki_preview_iframe($preview_link);
    }

    public function template_type($post)
    {
        $template_id = get_the_ID();
        $type = get_post_meta($template_id, 'template_type', true);
        $is_new = 'none';

        if (isset($_GET['post_type']) && $_GET['post_type'] == 'nonaki' && isset($_GET['subtype'])) {
            $subtype = sanitize_text_field($_GET['subtype']);
            $is_new = $subtype;
        }


        if (isset($_GET['post_type']) && $_GET['post_type'] == 'nonaki' && isset($_GET['type'])) {
            $type = sanitize_text_field($_GET['type']);
        }
        Render::select('nonaki_type', $type, nonaki_get_template_types());
?>
        <hr>

        <?php wp_nonce_field('nonaki_template_type_action', 'nonaki_template_type_nonce_field'); ?>

        <div data-is-new="<?php echo esc_attr($is_new) ?>" data-template-id="<?php echo esc_attr($template_id) ?>" id="sub_types_div"></div>
    <?php
        do_action('nonaki/metabox/type');
    }

    function template_type_status($post)
    {
        $status = get_post_meta(get_the_ID(), 'nonaki_status', true);
    ?>

        <div id="template_status_box">

            <select name="nonaki_status" class="nk-full-width">
                <option <?php selected($status, 'inactive') ?> value="inactive"><?php echo esc_html__("Inactive", "nonaki") ?></option>
                <option <?php selected($status, 'active') ?> value="active"><?php echo esc_html__("Active", "nonaki") ?></option>

            </select>
        </div>
        <?php
    }

    public function codemirror_enqueue_scripts()
    {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor(array(
            'type' => 'text/css',
            'read_only' => true
        ));
        wp_localize_script('jquery', 'cm_settings', $cm_settings);
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
    }

    public function source_code_metabox_content($post)
    {
        $content = $post->post_content;
        if (empty($content)) {
        ?>
            <div class="editor-preview">
                <small id="nki-not-found"><?php echo esc_html__("No content found ¯\_(ツ)_/¯", "nonaki") ?></small>
                <a class="button button-primary button-large" href="<?php echo esc_url(admin_url('index.php?page=nonaki&id=' . get_the_ID())) ?>"> <?php echo esc_html__("Edit Template With Builder ⚡️", "nonaki") ?></a>
            </div>
        <?php
        } else {

            echo '<textarea id="nonaki-template-code">' . esc_textarea($content) . '</textarea>';
        }
    }

    public function send_mail($post)
    {
        $current_user = wp_get_current_user();
        ?>
        <label>To</label>
        <select class="nk-full-width" id="nonaki_receiver_type">
            <option value="custom"><?php echo esc_html__("Custom email address", "nonaki") ?></option>
            <option value="site_user"><?php echo esc_html__("Site User", "nonaki") ?></option>
        </select>
        <select class="nk-full-width sender-input-select" id="nonaki_to">
            <?php
            foreach (get_users() as $user) {
            ?>
                <option value="<?php echo sanitize_email($user->data->user_email) ?>"><?php echo esc_html($user->data->user_login) ?></option>
            <?php

            }
            ?>
        </select>

        <input class="nk-full-width sender-input-txt" placeholder="Where you want to send" type="text" id="nonaki_to">
        <hr>
        <label><?php echo esc_html__("From", "nonaki") ?></label>
        <input class="nk-full-width" value="<?php echo sanitize_email($current_user->user_email) ?>" type="text" id="nonaki_from">
        <hr>
        <label><?php echo esc_html__("Subject", "nonaki") ?></label>
        <input class="nk-full-width" value="<?php echo esc_html($post->post_title) ?>" type="text" id="nonaki_subject">
        <hr>
        <label><?php echo esc_html__("Select Email Provider", "nonaki") ?></label>
        <select id="nonaki_provider" class="nk-full-width">
            <option value="wp"><?php echo esc_html__("Default", "nonaki") ?></option>
            <!-- <option value="mailgun"><?php echo esc_html__("Mailgun", "nonaki") ?></option> -->
        </select>
        <button type="button" class="nk-full-width button button-primary button-large" id="nonaki_send_mail_btn"><?php echo esc_html__("Send Mail", "nonaki") ?></button>
        <?php
    }

    public function page_css()
    {
        global $post;

        if ($post) {
            if ($post->post_type == $this->get_post_type()) {
        ?>
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions {
                        display: none;
                    }

                    .nk-full-width {
                        width: 100%;
                        margin-bottom: 10px !important;
                    }

                    .editor-preview {
                        text-align: center;
                        width: 100%;
                        padding-top: 20px;
                        padding-bottom: 20px;
                    }

                    .notice {
                        display: none;
                    }

                    /* .postbox {
                        border: none !important;
                        border-radius: 5px;
                        box-shadow: 0 10px 10px rgb(0 0 0 / 25%) !important;
                    }

                    #titlewrap>#title {
                        border: none !important;
                        box-shadow: 0 10px 10px rgb(0 0 0 / 25%) !important;
                    }

                    #template_type_box {
                        padding-top: 20px;
                    } */

                    #delete-action {
                        display: none !important;
                    }

                    #publishing-action {
                        width: 100% !important;
                    }

                    .nonakiTstMailFields {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 20px;
                    }

                    .nonakiTstMailFields>label {
                        font-size: medium;
                    }

                    .nonakiTstMailFields>input {
                        width: 400px;
                    }

                    #tstMailBox {
                        height: 100%;
                        width: 100%;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;

                    }


                    #tstMailHeader>h2 {
                        color: gray;
                        text-align: center;
                    }

                    #tstMailBody {
                        flex-grow: 1;
                    }

                    #tstMailFooter {
                        text-align: right;
                    }

                    #TB_ajaxContent {
                        background-color: #2C3338;
                        color: #FFFCE5;

                    }

                    .nonaki-content-box {
                        height: 258px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    #nki-btn-edit {
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        margin-top: 10px;
                        margin-left: 10px;
                    }

                    #nki-btn-publish {
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        margin-top: 10px;
                        margin-left: 10px;
                    }

                    .nki-top-section {
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        margin-top: 10px;
                        margin-left: 10px;
                    }

                    #nki-btn-preview {
                        margin-left: 10px;
                        margin-top: 10px;

                        display: inline-flex;
                        align-items: center;
                        gap: 5px
                    }

                    .nki-svg-icon {
                        width: 15px;
                    }

                    #quickPreviewBtn {
                        margin-left: 10px;
                        display: flex;
                        align-items: center;
                        gap: 5px
                    }

                    #testMailBtn {
                        margin-left: 10px;
                        margin-top: 10px;
                        display: inlie-flex;
                        align-items: center;
                        gap: 5px
                    }

                    #test-mail-modal-window-id {
                        display: none;
                    }

                    #ntstSendBtn {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 5px
                    }

                    #nki-not-found {
                        display: block;
                        margin-bottom: 20px
                    }

                    #nonaki_to {
                        display: none;
                    }

                    #nonaki-status-box {
                        display: flex;
                        align-items: center;
                        gap: 5px
                    }
                </style>
        <?php
            }
        }
    }

    public function change_button_text($translation, $text)
    {
        if ($text == 'Publish') {
            return 'Save ';
        }

        return $translation;
    }



    public function admin_script()
    {
        global $post;
        $nonaki_template_editing_url = admin_url('/') . 'index.php?page=nonaki&id=' . get_the_ID();
        $nonaki_template_preview_url = $nonaki_template_editing_url . '&mood=preview';

        $current_user = wp_get_current_user();
        add_thickbox();
        ?>

        <div id="test-mail-modal-window-id">
            <div id="tstMailBox">
                <div id="tstMailHeader">
                    <h2><?php echo esc_html__("Send Test Mail", "nonaki") ?></h2>

                </div>
                <div id="tstMailBody">
                    <div class="nonakiTstMailFields">
                        <label><?php echo esc_html__("To", "nonaki") ?></label>
                        <input autofocus type="email" id="ntstTo" placeholder="Recipient email address" />
                    </div>
                    <div class="nonakiTstMailFields">
                        <label><?php echo esc_html__("From", "nonaki") ?></label>
                        <input value="<?php echo sanitize_email($current_user->user_email) ?>" type="email" id="ntstFrom" placeholder="Sender email address" />
                    </div>
                    <div class="nonakiTstMailFields">
                        <label><?php echo esc_html__("Subject", "nonaki") ?></label>
                        <input value="<?php echo esc_html($post->post_title) ?>" type="text" id="ntstSubject" placeholder="Email subject" />
                    </div>
                </div>

                <div id="tstMailFooter">
                    <button id="ntstSendBtn" class="button button-primary"><i class="dashicons dashicons-email"></i> <?php echo esc_html__("Send now", "nonaki") ?></button>
                </div>

            </div>
        </div>
        <script>
            var $ = jQuery;
            var templateID = $('#sub_types_div').attr('data-template-id');
            var isNew = $('#sub_types_div').attr('data-is-new');

            $('#quickPreviewBtn').on("click", function() {
                $('#nonaki_quick_preview_metabox').toggle(200);
            })

            if ($('#nonaki-template-code').length > 0) {
                jQuery(jQuery(".wrap .page-title-action")[0]).after('<a href="#" class="page-title-action nonakiBtnCopyCode">Copy HTML code</a>');
                $('.nonakiBtnCopyCode').on("click", function(e) {
                    e.preventDefault();
                    var copyText = document.getElementById("nonaki-template-code");
                    nonakiCopyTemplateCode(copyText.value)
                    alert('Copied')
                })

            }

            // Toolbar buttons


            if ($('#publish').val() === 'Update') {

                jQuery(jQuery(".wrap .wp-heading-inline")[0]).after(`<a id="testMailBtn" class="page-title-action thickbox" href="#TB_inline?width=200&height=300&inlineId=test-mail-modal-window-id">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 nki-svg-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <?php echo esc_html__("Send Test Mail", "nonaki") ?>

            </a>`);


                jQuery(jQuery(".wrap .wp-heading-inline")[0]).after(`<a id="nki-btn-edit" class="button button-primary" href="<?php echo esc_url($nonaki_template_editing_url) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 nki-svg-icon " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg> <?php echo esc_html__("Edit Template", "nonaki") ?>
            </a>`);
            } else {
                jQuery(jQuery(".wrap .wp-heading-inline")[0]).after(`<p style="background: yellow;
    padding: 0px 5px;
    border-radius: 5px;" class="nki-top-section"> <?php echo esc_html__("In order to edit the template, you need to publish it first.", "nonaki") ?></p>`);

                jQuery(jQuery(".wrap .wp-heading-inline")[0]).after(`<a id="nki-btn-publish" class="button button-primary nki-top-btns" href="#">
                 <?php echo esc_html__("Publish Template", "nonaki") ?>
            </a>`);

                jQuery('#nki-btn-publish').on('click', function() {
                    jQuery('#publish').trigger('click');
                })


            }



            function nonakiCopyTemplateCode(text) {
                var input = document.createElement('input');
                input.setAttribute('value', text);
                document.body.appendChild(input);
                input.select();
                var result = document.execCommand('copy');
                document.body.removeChild(input);
                return result;
            }



            $('#nonaki_send_mail_btn').on("click", function() {
                let nonaki_receiver;
                if ($('#nonaki_receiver_type').val() == 'custom') {
                    nonaki_receiver = $('.sender-input-txt').val();
                } else {
                    nonaki_receiver = $('.sender-input-select').val();
                }

                if (nonaki_receiver == '') {
                    $(".sender-input-txt").focus();
                    return alert('You must enter receiver email address');
                }


                nonaki_send_mail(
                    nonaki_receiver,
                    $('#nonaki_from').val(),
                    $('#nonaki_subject').val(),
                    $('#nonaki_provider').val(),
                    'general'
                )
            })

            $('#ntstSendBtn').on("click", function() {
                if ($('#ntstTo').val() == '') {
                    return alert('Recipient email required')
                }

                nonaki_send_mail(
                    $('#ntstTo').val(),
                    $('#ntstFrom').val(),
                    $('#ntstSubject').val(),
                    'wp',
                    'test'
                )
            })

            function nonaki_send_mail(to, from, subject, provider, type) {
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'nk_send_mail',
                        to: to,
                        from: from,
                        subject: subject,
                        provider: provider,
                        template_id: templateID,
                        type: type
                    },
                    success: function(data) {
                        alert('Sent');
                    },
                    error: function(data) {
                        alert("Something went wrong");
                        console.log(data.responseText);
                    }
                });
            }

            function nonaki_get_sub_types(type) {

                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'nk_get_sub_types',
                        sub_type: type,
                        is_new: isNew,
                        template_id: templateID,
                    },
                    success: function(data) {
                        $('#sub_types_div').html(data);
                    },
                    error: function(data) {
                        alert('Something went wrong');
                        console.log(data);
                    }
                })
            }

            nonaki_get_sub_types($('#nonaki_type').val());

            $('#nonaki_type').on('change', function() {

                var subTypeVal = $(this).val();
                nonaki_get_sub_types(subTypeVal);
                // $('#publish').trigger('click')

            });

            $('#nonaki_receiver_type').on('change', function() {
                let val = $(this).val();
                if (val === 'custom') {
                    $('.sender-input-select').hide();
                    $('.sender-input-txt').show();
                } else {
                    $('.sender-input-select').show();
                    $('.sender-input-txt').hide();
                }

            })

            function nonakiReadySendingOptions() {
                let val = $('#nonaki_receiver_type').val();
                if (val === 'custom') {
                    $('.sender-input-select').hide();
                    $('.sender-input-txt').show();
                } else {
                    $('.sender-input-select').show();
                    $('.sender-input-txt').hide();
                }
            }

            jQuery(document).ready(function($) {
                if ($('#nonaki-template-code').length > 0) {
                    wp.codeEditor.initialize($('#nonaki-template-code'), cm_settings);
                }

                nonakiReadySendingOptions();

            })
        </script>
<?php
    }
}
