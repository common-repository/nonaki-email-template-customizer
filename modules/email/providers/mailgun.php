<?php

namespace Nonaki\Modules\Email;

class Mailgun implements Provider
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($to, $from, $subject, $content)
    {
        return $this->send_mailgun_message($to, $from, $subject, $content);
    }

    private function send_mailgun_message($emails, $from, $subject, $content)
    {

        $mail_data = [];
        $mail_data['from'] = $from;
        $mail_data['to'] = implode(', ', $emails);
        $mail_data['subject'] = $subject;
        $mail_data['html'] = $content;
        $url = 'https://api.mailgun.net/v3/' . $this->config['domain'] . '/messages';
        $response = wp_remote_post(
            $url,
            array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,

                'body'        => array(
                    "CURLOPT_USERPWD" => 'api:' . $this->config['api'],
                    "CURLOPT_RETURNTRANSFER" => 1,
                ),
                'cookies'     => array()
            )
        );

        return $response;
    }
}
