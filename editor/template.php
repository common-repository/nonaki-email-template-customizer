<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url($favicon_url); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo esc_html($nonaki_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .nonaki-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 99;
            background-color: #242B3B;
        }


        body {
            padding: 0px;
            margin: 0px;
        }

        .gjs-pn-panels {
            position: absolute;
            bottom: 0px;
            right: 0px;
        }

        .gjs-blocks-no-cat,
        .gjs-block-categories {
            border-radius: 5px;
            border: 1px solid #4B5663;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        #preloader {
            width: 100%;
            height: 100%;
            margin: 0px !important;
            padding: 0px;
            z-index: 100;
            position: absolute;
            background-size: 400% 400%;
            background-color: #9CA8BB;
        }

        .properties {
            border: 1px solid #4B5663;
            border-radius: 7px;
            padding: 0px;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .preloader-item {

            /* Center vertically and horizontally */
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -25px 0 0 -25px;
            /* apply negative top and left margins to truly center the element */
        }

        .panel__basic-actions {
            background-color: #242B3B !important;
            border-radius: 10px;
        }

        .gjs-one-bg {
            background-color: #242B3B !important;
        }

        #main {
            height: 100%;

        }

        #layers {
            flex-basis: 290px;
            background-color: #242B3B;
        }

        #editor {
            overflow: hidden;
        }

        #editor-header {
            background-color: #242B3B;
        }

        #style-manager {
            flex-basis: 280px;
            background-color: #242B3B;
        }
    </style>
    <script>
        window.site_url = "<?php echo esc_url(site_url()); ?>"
        var nonakiData = <?php echo $nonaki_data ?>
    </script>


    <script type="module" crossorigin src="<?php echo esc_url($editor_js) ?>"></script>
    <link rel="stylesheet" href="<?php echo esc_url($editor_css) ?>">

    <?php do_action('nonaki_editor_css') ?>

</head>

<body>

    <div id="preloader">
        <span class="preloader-item"><img width="100" height="100" src="<?php echo esc_url($favicon_url) ?>"></span>
    </div>

    <!-- 
    # Main Editor Section starts here #
   -->
    <div id="main" class="row">

        <!-- Editor Layers section start -->

        <div id="layers" class="column flex px-2 py- flex-col h-screen justify-between">
            <div class="nonaki-sticky pt-2">
                <div class="hero-section shadow-lg rounded-md text-white mb-2  flex justify-between items-center py-1 px-2 gap-2">
                    <a href="<?php echo esc_url($nonaki_nonaki_exit_url) ?>">
                        <!-- <svg class="w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg> -->
                    </a>
                    <!-- Nonaki text is not translatable -->
                    <div class="text-xl">Nonaki</div>
                    <div>
                        <!-- <a id="btnImportTemplate" href="#">

                            <svg class="w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd" />
                                <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z" />
                            </svg>
                        </a> -->
                    </div>
                </div>

                <input id="element_search_box" placeholder="Search Blocks ...." type="text" class="py-2 w-full  px-3 outline-none bg-gray-600 my-2 text-gray-200 text-sm rounded-md shadow-xl flex justify-center gap-2" />
            </div>
            <div class="h-full" id="blocks"></div>
            <div id="elements"></div>

        </div>

        <!-- Editor Layers Section end -->

        <!-- Email content section start -->
        <div class="column editor-clm">
            <div id="editor-header" class=" flex w-full justify-between  items-center">
                <div class="px-2">
                    <div class="relative inline-block text-left">
                        <div>
                            <button id="menu-btn" type=" button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md  px-3 py-2 text-sm font-semibold text-white shadow-sm " id="menu-button" aria-expanded="true" aria-haspopup="true">
                                Menu
                                <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div id="menu-dropdown" class="absolute hidden left-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-500 rounded-md bg-gray-800 backdrop-blur-3xl shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-200" -->
                                <a href="#" class="text-gray-200 block px-4 py-2 text-sm menu-btn" role="menuitem" tabindex="-1" id="presetsBtn">Presets</a>

                            </div>


                        </div>
                    </div>
                    <div class="relative inline-block text-left">
                        <div>
                            <button id="edit-btn" type=" button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md  px-3 py-2 text-sm font-semibold text-white shadow-sm " id="menu-button" aria-expanded="true" aria-haspopup="true">
                                Edit
                                <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div id="edit-dropdown" class="absolute hidden left-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-500 rounded-md bg-gray-800 backdrop-blur-3xl shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-200" -->
                                <a href="#" class="text-gray-200 block px-4 py-2 text-sm menu-btn" role="menuitem" tabindex="-1" id="undoBtn">Undo</a>
                                <a href="#" class="text-gray-200 block px-4 py-2 text-sm menu-btn" role="menuitem" tabindex="-1" id="redoBtn">Redo</a>
                            </div>
                            <div class="py-1" role="none">
                                <a href="#" class="text-gray-200 block px-4 py-2 text-sm menu-btn" role="menuitem" tabindex="-1" id="clearAll">Clear All</a>
                                <a href="#" class="text-gray-200 block px-4 py-2 text-sm menu-btn" role="menuitem" tabindex="-1" id="clearHistory">Clear History</a>
                            </div>

                        </div>
                    </div>

                </div>
                <div>
                    <div id="devices-c"></div>
                </div>



            </div>

            <div id="editor" data-save-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" data-post-id="<?php echo esc_attr($nonaki_post_id) ?>" data-title="<?php echo esc_html($nonaki_title) ?>" assets-url="<?php echo esc_url(nonaki_get_assets_url()) ?>" preset-url="<?php echo esc_url(nonaki_get_presets_url()) ?>">
                <?php if ($nonaki_editor_mode === 'mail' && empty($nonaki_content)) : ?>
                    <mjml>
                        <mj-body>
                            <?php echo nonaki_e($nonaki_content) ?>
                        </mj-body>
                    </mjml>
                <?php else : ?>
                    <?php echo nonaki_e($nonaki_content) ?>
                <?php endif; ?>

            </div>
        </div>
        <!-- Email Content section end -->

        <!-- Editor Toolsbar start -->
        <div id="style-manager" class="column px-2">

            <div class="nonaki-sticky py-2 text-white mb-2  flex justify-center gap-2 items-center">
                <a id="nkSaveBtn" class="bg-green-600 shadow-md rounded-md px-3  flex items-center gap-1" href="#">
                    <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <?php esc_html_e("Save", "nonaki"); ?>
                </a>

                <a id="btn-cancel" class="bg-pink-600 shadow-md rounded-md px-4 flex items-center gap-1" href="<?php echo esc_url($nonaki_nonaki_exit_url) ?>">
                    <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <?php esc_html_e("Exit", "nonaki"); ?>
                </a>
            </div>

            <div class="panel__top rounded w-full flex justify-center">
                <div class="panel__basic-actions"></div>
            </div>

            <div class="properties pb-2">
                <div class="py-2 bg-gray-600 text-gray-100 rounded-md shadow-md flex justify-between px-3 gap-3"> <i class="fa fa-paint-brush"></i>
                    <div class=" text-sm text-gray-400"><?php esc_html_e("Style", "nonaki") ?></div>
                </div>
                <div id="style-manager-container"></div>
            </div>

            <div class="properties pb-2">
                <div class="py-2 bg-gray-600 text-gray-100 rounded-md shadow-md flex justify-between px-3 gap-3"> <i class="fa fa-cog"></i>
                    <div class=" text-sm text-gray-400"><?php esc_html_e("Attributes", "nonaki"); ?> </div>
                </div>
                <div id="traits-container"> </div>
            </div>

            <div class="flex flex-col properties">
                <div class="py-2  bg-gray-600 text-gray-100 flex justify-between px-3 gap-3 rounded-md shadow-md">

                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="layer-group" class="w-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="currentColor" d="M12.41 148.02l232.94 105.67c6.8 3.09 14.49 3.09 21.29 0l232.94-105.67c16.55-7.51 16.55-32.52 0-40.03L266.65 2.31a25.607 25.607 0 0 0-21.29 0L12.41 107.98c-16.55 7.51-16.55 32.53 0 40.04zm487.18 88.28l-58.09-26.33-161.64 73.27c-7.56 3.43-15.59 5.17-23.86 5.17s-16.29-1.74-23.86-5.17L70.51 209.97l-58.1 26.33c-16.55 7.5-16.55 32.5 0 40l232.94 105.59c6.8 3.08 14.49 3.08 21.29 0L499.59 276.3c16.55-7.5 16.55-32.5 0-40zm0 127.8l-57.87-26.23-161.86 73.37c-7.56 3.43-15.59 5.17-23.86 5.17s-16.29-1.74-23.86-5.17L70.29 337.87 12.41 364.1c-16.55 7.5-16.55 32.5 0 40l232.94 105.59c6.8 3.08 14.49 3.08 21.29 0L499.59 404.1c16.55-7.5 16.55-32.5 0-40z">
                        </path>
                    </svg>
                    <div class=" text-sm text-gray-400"><?php esc_html_e("Layers", "nonaki") ?></div>
                </div>
                <div class="py-2" id="layers-container"></div>
            </div>

        </div>
        <!-- Editor Toolsbar end -->

    </div>