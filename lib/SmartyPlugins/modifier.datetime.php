<?php
// {{{ smarty_modifier_datetime
/**
 *  smarty modifier:datetime()
 *
 *  datetime()関数のwrapper
 *
 *  sample:
 *  <code>
 *  {"2004/01/01 01:01:01"|datetime:"%Y年%m月%d日"}
 *  </code>
 *  <code>
 *  2004年01月01日
 *  </code>
 *
 *  @param  string  $string フォーマット対象文字列
 *  @param  string  $format 書式指定文字列(datetime()関数参照)
 *  @return string  フォーマット済み文字列
 */
function smarty_modifier_datetime($string, $format = 'Ymd')
{
    if ($string === '' || is_null($string)) {
        return '';
    }
    $str = '';
    if (preg_match('/^\d{2,4}-\d{2}-\d{2}|\d{2,4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $string)) {
        // YYYY-MM-DD形式
        switch ($format) {
            case 'YmdHis':
                $str = date('Y年m月d日 H時i分s秒', strtotime($string));
                break;
            case 'ymdHis':
                $str = date('y年m月d日 H時i分s秒', strtotime($string));
                break;
            case 'YmdHi':
                $str = date('Y年m月d日 H時i分', strtotime($string));
                break;
            case 'ymdHi':
                $str = date('y年m月d日 H時i分', strtotime($string));
                break;
            case 'Ymd':
                $str = date('Y年m月d日', strtotime($string));
                break;
            case 'Ym':
                $str = date('Y年m月', strtotime($string));
                break;
            case 'ymd':
                $str = date('y年m月d日', strtotime($string));
                break;
            case 'md':
                $str = date('m月d日', strtotime($string));
                break;
            case 'Hi':
                $str = date('H時i分', strtotime($string));
                break;
            default:
                $str = date($format, strtotime($string));
                break;
        }
    }
    return $str;
}?>
