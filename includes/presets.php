<?php

namespace Nonaki;

class Presets
{
    use BaseTrait;

    public function __construct()
    {
        add_action('admin_head', [$this, 'page_css']);
    }

    public function page_content()
    {

        $create_template_url = NONAKI_EDITOR_URL;
?>
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" class="n_preset_svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd" />
                <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z" />
            </svg>

            <?php esc_html_e("Presets", "nonaki"); ?>
        </h1>
        <hr>

        <div class="n_presets">
            <?php
            foreach (nonaki_get_presets() as $template_id => $presets) {

                $template_link = esc_url($create_template_url . '&preset=' . $template_id);
            ?>
                <div class="n_preset_item">
                    <img src="<?php echo esc_url($presets['image']); ?>" />
                    <h2><?php echo esc_html($presets['name']) ?></h2>
                    <a target="__blank" href="<?php echo esc_url($template_link); ?>"><?php echo esc_html__('Create template', 'nonaki') ?></a>
                </div>
            <?php } ?>

        </div>
    <?php


    }

    public function page_css()
    {

        $currentScreen = get_current_screen();

        if ($currentScreen->id != "nonaki_page_nonaki-presets") {
            return;
        }

    ?>

        <style>
            .n_presets {
                display: grid;
                grid-gap: 50px 50px;
                grid-template-columns: auto auto auto;
                padding: 10px 20px 10px 0px;
            }

            .n_preset_item {
                background: #d4d4d4;
                border-radius: 5px;
                box-shadow: 0 10px 10px rgb(0 0 0 / 25%);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;

            }

            .n_preset_item>img {
                margin: 0px;
                padding: 0px;
                /* max-width: 350px; */
                width: 100%;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }

            .n_preset_item>a {
                width: 100% !important;
                background-color: #242B3B;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
                text-decoration: none !important;
                text-align: center;
                color: white;
                padding: 10px 0px;
                font-size: 1rem;
            }

            .n_preset_svg {
                width: 25px;
            }
        </style>

<?php
    }
}
