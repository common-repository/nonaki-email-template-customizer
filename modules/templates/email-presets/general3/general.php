<?php

ob_start();
include "content.php";
$nonaki_business_presets = ob_get_contents();
ob_clean();
$preview_image = "https://res.cloudinary.com/prappo/image/upload/v1681193522/email%20templates/general4_lvcgnc.png"; // 620 x 450 image size
add_nonaki_email_preset("general3", "General Email template", $preview_image, $nonaki_business_presets);
