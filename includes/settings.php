<?php

namespace Nonaki;

class Settings extends Base
{

    use BaseTrait;

    private $settings_group = 'nonaki-option-group';

    private $settings_fields = [
        'nonaki_provider_mailgun_domain',
        'nonaki_provider_mailgun_api',
        'nonaki_enable_source_code',
        'nonaki_enable_email_sending',
        'nonaki_enable_woocommerce',
    ];

    public function init()
    {
        add_action('admin_init', [$this, 'register_nonaki_settings']);
        add_action('admin_head', [$this, 'page_css']);
    }

    public function register_nonaki_settings()
    {
        foreach ($this->settings_fields as $field) {
            register_setting($this->settings_group, $field);
        }
    }

    public function settings_page()
    {
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
        $display_provider_settings = false;
        $display_template_settings = false;
        $display_editor_settings = false;

?>

        <div class="wrap">
            <!-- Print the page title -->
            <h1><?php esc_html_e(get_admin_page_title(), "nonaki") ?></h1>
            <!-- Here are our tabs -->
            <nav class="nav-tab-wrapper">
                <a href="?page=nonaki-settings" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">Template</a>
                <!-- <a href="?page=nonaki-settings&tab=email_provider" class="nav-tab <?php if ($tab === 'email_provider') : ?>nav-tab-active<?php endif; ?>">Email Provider</a> -->

            </nav>

            <div class="tab-content">
                <form id="settings-form" method="post" action="options.php">
                    <?php settings_fields('nonaki-option-group'); ?>
                    <?php do_settings_sections('nonaki-option-group'); ?>

                    <?php switch ($tab):
                        case 'email_provider':
                            $display_provider_settings = true;
                            break;
                        case 'editor':
                            $display_editor_settings = true;
                            break;
                        default:
                            $display_template_settings = true;
                            break;
                    endswitch; ?>

                    <div style="<?php echo esc_attr($display_provider_settings ?: 'display:none') ?>">
                        <small><?php esc_html_e("Email Provider Settings", "nonaki"); ?></small>
                        <hr>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e("Mailgun Domain", "nonaki") ?></th>
                                <td><input type="text" name="nonaki_provider_mailgun_domain" value="<?php esc_html_e(get_option('nonaki_provider_mailgun_domain'), "nonaki"); ?>" /></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row"><?php esc_html_e("Mailgun API", "nonaki") ?></th>
                                <td><input type="text" name="nonaki_provider_mailgun_api" value="<?php esc_html_e(get_option('nonaki_provider_mailgun_api'), "nonaki"); ?>" /></td>
                            </tr>

                        </table>
                        <hr>
                    </div>

                    <div style="<?php echo esc_attr($display_template_settings)  ?: 'display:none' ?>">
                        <small><?php esc_html_e("Template Settings", "nonaki") ?></small>
                        <hr>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php esc_html_e("Show Source Code", "nonaki") ?></th>
                                <td><input type="checkbox" id="nonaki_enable_source_code" name="nonaki_enable_source_code" value="1" <?php checked(1, get_option('nonaki_enable_source_code'), true); ?> /></td>
                            </tr>


                            <tr valign="top">
                                <th scope="row"><?php esc_html_e("Email Sending Option", "nonaki") ?></th>
                                <td><input type="checkbox" id="nonaki_enable_email_sending" name="nonaki_enable_email_sending" value="1" <?php checked(1, get_option('nonaki_enable_email_sending'), true); ?> /></td>
                            </tr>



                        </table>
                        <hr>
                    </div>

                    <div style="<?php echo esc_attr($display_editor_settings) ?: 'display:none' ?>">
                        <small><?php esc_html_e("Editor Settings", "nonaki") ?></small>
                        <hr>
                        <table class="form-table">


                        </table>
                        <hr>
                    </div>
                    <?php submit_button(); ?>

                </form>
            </div>
        </div>


    <?php
    }

    public function page_css()
    {
        $currentScreen = get_current_screen();

        if ($currentScreen->id != "nonaki_page_nonaki-settings") {
            return;
        }

    ?>

        <style>
            #settings-form {
                margin-top: 10px;
            }
        </style>

<?php
    }
}
