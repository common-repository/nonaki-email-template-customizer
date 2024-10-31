<?php

namespace Nonaki\Modules\Email;

use Nonaki\Modules\Email\Provider;

class WP_Mail_Service implements Provider
{
    public function __construct()
    {
        add_filter('wp_mail_content_type', function () {
            return "text/html";
        });
    }
    public function send($to, $from, $subject, $content)
    {
        $headers[] = 'From: <' . $from . '>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        return wp_mail($to, $subject, $content);
    }
}
