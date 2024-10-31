<?php

namespace Nonaki;

class Shortcode extends Base
{
    use BaseTrait;

    public function init()
    {
        add_shortcode("nonaki", [$this, "shortcode_content"]);
    }

    public function shortcode_content($atts)
    {
        $attributes = shortcode_atts([
            'id' => ''
        ], $atts);


        return nonaki_get_email_template($attributes['id']);
    }
}
