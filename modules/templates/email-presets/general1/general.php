<?php

ob_start();
include "content.php";
$nonaki_business_presets = ob_get_contents();
ob_clean();
$preview_image = "https://res.cloudinary.com/prappo/image/upload/v1681193162/email%20templates/general2_is2ois.png"; // 620 x 450 image size
add_nonaki_email_preset("general1", "General Email Template", $preview_image, $nonaki_business_presets);
