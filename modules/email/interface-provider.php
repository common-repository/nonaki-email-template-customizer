<?php 
namespace Nonaki\Modules\Email;

interface Provider {
    public function send($to,$from,$subject,$content);
}

