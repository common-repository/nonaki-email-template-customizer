<?php

namespace Nonaki;


class Render extends Base
{
    use BaseTrait;

    public function init()
    {
    }

    public static function select($id, $selected, $arr)
    {
        if (!is_array($arr)) {
            return;
        }
?>
        <div>

            <select id="<?php esc_html_e($id, "nonaki"); ?>" name="<?php esc_html_e($id, "nonaki") ?>" class="nk-full-width">
                <?php foreach ($arr as $type_name => $type_lable) : ?>
                    <option <?php selected($selected, $type_name) ?> value="<?php esc_html_e($type_name, "nonaki") ?>"><?php esc_html_e($type_lable, "nonaki") ?></option>
                <?php endforeach; ?>

            </select>

            <script>
                // jQuery('#<?php esc_html_e($id, "nonaki"); ?>').on('change', function() {
                //     jQuery('#publish').trigger('click')
                // })
            </script>
        </div>
<?php
        return;
    }

    public static function status_icon($status)
    {
        $color = '#3B82F6';
        switch ($status) {
            case 'active':
                $color = '#10B981';
                break;
            case 'inactive':
                $color = '#FBBF24';
                break;
        }

        return '<svg  style="width: 15px;color: ' . $color . ' !important" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
</svg>';
    }

    public static function status_text($text, $status)
    {
        $color = '#3B82F6';
        switch ($status) {
            case 'active':
                $color = '#10B981';
                break;
            case 'inactive':
                $color = '#FBBF24';
                break;
        }
        return '<div style="background: ' . $color . ';padding:2px;text-align: center;
    border-radius: 3px;color:white">
          ' . $text . '
        </div>';
    }
}
