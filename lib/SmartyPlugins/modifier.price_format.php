<?php
/**
 *  smarty_modifier:strim()
 *
 *  sample:
 *  <code>
 *  {"12345"|strim:5}
 *  </code>
 *  <code>
 *  12...
 *  </code>
 *
 *  @param  string  $str  金額
 *  @return string  フォーマット済み文字列
 */
function smarty_modifier_price_format($str)
{
    if (!$str) {
        $str = 0;
    }
    return '&yen;' . number_format($str);
}
?>
