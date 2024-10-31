<?php

namespace Nonaki;

abstract class Base
{
    private $post_type = 'nonaki';

    public function get_post_type()
    {
        return $this->post_type;
    }
    public abstract function init();
}
