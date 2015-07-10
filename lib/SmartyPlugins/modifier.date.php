<?php
// {{{ smarty_modifier_date
/**
 *  smarty modifier:date()
 *
 *  date()関数のwrapper
 *
 *  sample:
 *  <code>
 *  {"2004/01/01 01:01:01"|date:"%Y年%m月%d日"}
 *  </code>
 *  <code>
 *  2004年01月01日
 *  </code>
 *
 *  @param  string  $string フォーマット対象文字列
 *  @param  string  $format 書式指定文字列(date()関数参照)
 *  @return string  フォーマット済み文字列
 */
function smarty_modifier_date($string, $format)
{
    if ($string === "" || $string == null) {
        return "";
    }
    if (preg_match('/^[\d]{4}\-[\d]{4}\-[\d]{4}$/', $string)) {
        return date($format, strtotime($string));
    } else {
        return date($format, $string);
    }
}?>
