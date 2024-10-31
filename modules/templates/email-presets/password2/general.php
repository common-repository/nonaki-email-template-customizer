<?php

ob_start();
include "content.php";
$nonaki_business_presets = ob_get_contents();
ob_clean();
$preview_image = "https://res.cloudinary.com/prappo/image/upload/v1681194015/email%20templates/password2_barogg.png"; // 620 x 450 image size
add_nonaki_email_preset("password2", "Password reset Email template", $preview_image, $nonaki_business_presets);
