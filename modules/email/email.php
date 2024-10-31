<?php

namespace Nonaki\Modules\Email;

use Exception;

require_once 'interface-provider.php';
require_once 'providers/wp-mail-service.php';
require_once 'providers/mailgun.php';

class Email
{
    private $provider = null;
    private $to = [];
    private $from;
    private $subject;
    private $content;

    public function to($email_address)
    {
        $this->set_email_addresses($email_address);
        return $this;
    }

    public function from($email_address)
    {
        $this->from = $email_address;
        return $this;
    }

    public function set_subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function set_content($content)
    {
        $this->content = $content;
        return $this;
    }

    public function set_provider(Provider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    private function set_email_addresses($email_address)
    {
        $this->to = explode(',', $email_address);
    }

    public function send()
    {
        if ($this->provider == null) {
            throw new Exception('Email provider required');
        }

        return $this->provider->send(
            $this->to,
            $this->from,
            $this->subject,
            $this->filter_content($this->content),
        );
    }

    private function filter_content($content)
    {
        $content = apply_filters('nonaki_email_content', $content);
        return $content;
    }
}
