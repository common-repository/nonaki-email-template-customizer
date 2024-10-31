<?php

ob_start();
include "content.php";
$nonaki_business_presets = ob_get_contents();
ob_clean();
$preview_image = "https://res.cloudinary.com/prappo/image/upload/v1681193787/email%20templates/password-reset1_v3gg4n.png"; // 620 x 450 image size
add_nonaki_email_preset("password1", "Password reset Email template", $preview_image, $nonaki_business_presets);
