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
 *  @param  string   $string  フォーマット対象文字列
 *  @param  integer  $length  切捨て文字数（バイト）
 *  @param  string   $suffix  追加文字列
 *  @return string  フォーマット済み文字列
 */
function smarty_modifier_strim($string, $length, $suffix = '...')
{
    return mb_strimwidth($string, 0, $length, $suffix, 'UTF-8');
}
?>
