<?php

defined('ABSPATH') || exit;


?>

<div class="px-10">
    <div class="container m-auto py-10">
        <div class="grid grid-cols-3 gap-20">
            <div class="bg-white rounded-md shadow-sm flex flex-col gap-3 px-10 py-10">
                <div class="font-semibold text-2xl">Templates</div>
                <div class="font-bold text-4xl"><?php echo  esc_html(wp_count_posts('nonaki')->publish) ?></div>
            </div>
            <div class="bg-white rounded-md shadow-sm flex flex-col gap-3 px-10 py-10">
                <div class="font-semibold text-2xl">Contacts</div>
                <div class="font-bold text-4xl"><?php echo  esc_html(wp_count_posts('nonaki_contact')->publish) ?></div>
            </div>
            <div class="bg-white rounded-md shadow-sm flex flex-col gap-3 px-10 py-10">
                <div class="font-semibold text-2xl">Lists</div>
                <div class="font-bold text-4xl"><?php echo  esc_html(wp_count_terms('list')) ?></div>
            </div>
            <div class="bg-white rounded-md shadow-sm flex flex-col gap-3 px-10 py-10">
                <div class="font-semibold text-2xl">Total Mail Sent</div>
                <div class="font-bold text-4xl"><?php echo  esc_html(nonaki_get_total_mail_sent()) ?></div>
            </div>
        </div>
    </div>
</div>