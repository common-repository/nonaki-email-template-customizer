<?php
defined('ABSPATH') || exit;


$new_post_url_wp       = admin_url('/') . 'post-new.php?post_type=nonaki&type=WordPress';
$new_post_url_general  = admin_url('post-new.php?post_type=nonaki&type=general');
$nonaki_templates_type = array(
	array(
		'type'        => 'General Template',
		'title'       => 'General Template',
		'description' => 'Make pre-written format for creating and sending professional emails for various business purposes.',
		'icon'        => NONAKI_ASSETS_URL . '/images/General_Email_Template.svg',
		'url'         => esc_url($new_post_url_general),
	),
	array(
		'type'        => 'WordPress',
		'title'       => 'Default Email Layout Template',
		'description' => 'Default email layout template is a pre-set design and formatting for creating consistent emails across a business or organization.',
		'icon'        => NONAKI_ASSETS_URL . '/images/Default_Layout.svg',
		'url'         => admin_url('post-new.php?post_type=nonaki&type=wordpress&subtype=default'),
	),
	array(
		'type'        => 'WordPress',
		'title'       => 'Password Reset Email',
		'description' => 'Pre-written message template sent to a user to reset their password for their account.',
		'icon'        => NONAKI_ASSETS_URL . '/images/Password_Reset_Email.svg',
		'url'         => admin_url('post-new.php?post_type=nonaki&type=wordpress&subtype=password_reset'),
	),
	array(
		'type'        => 'WordPress',
		'title'       => 'New User Register Email Template',
		'description' => 'Make template for new user registration email template',
		'icon'        => NONAKI_ASSETS_URL . '/images/New_User_Notification_Email.svg',
		'url'         => admin_url('post-new.php?post_type=nonaki&type=wordpress&subtype=new_user_notification_email'),
	),
	array(
		'type'        => 'WordPress',
		'title'       => 'User Email Change Email Template',
		'description' => 'Make template for email change email when user change their email address',
		'icon'        => NONAKI_ASSETS_URL . '/images/New_User_Change_Email.svg',
		'url'         => admin_url('post-new.php?post_type=nonaki&type=wordpress&subtype=email_change_email'),
	),
	array(
		'type'        => 'WordPress',
		'title'       => 'Password Change',
		'description' => 'Make template for password change when user will change their password',
		'icon'        => NONAKI_ASSETS_URL . '/images/Password_Change_Email.svg',
		'url'         => admin_url('post-new.php?post_type=nonaki&type=wordpress&subtype=password_change_email'),
	),

);

$nonaki_templates_type = apply_filters('nonaki_templates', $nonaki_templates_type);
?>



<div class="px-5">
	<div class="container m-auto py-10">
		<div class="grid grid-cols-4 gap-10">

			<?php foreach ($nonaki_templates_type as $templates_type) { ?>
				<div class="flex justify-between flex-col gap-5 rounded-md bg-white px-10 py-10 shadow-sm hover:shadow-xl">
					<img class="w-20" src="<?php echo esc_url($templates_type['icon']); ?>" />
					<div class="text-2xl font-semibold"><?php echo esc_html($templates_type['title']); ?></div>
					<div class=""><?php echo esc_html($templates_type['description']); ?></div>
					<div><a href="<?php echo esc_url($templates_type['url']); ?>" type="button" class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Create</a></div>
				</div>

			<?php } ?>
		</div>
	</div>
</div>