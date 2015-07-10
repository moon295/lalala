<?php
/**
 *  lib/util/CommonUtil.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
*/

/**
 *  CommonUtilクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class CommonUtil
{
    function hashPassword($password)
    {
        $pass = md5($password);
        return md5(SALT . $pass . SALT);
    }

    /**
     *  ランダムな文字列を生成する
     *
     *  @access public
     *  @param  string  $len  生成文字列の長さ
     *  @return string  ランダムな文字列
     */
    function getRand($len = 8)
    {
        // 文字列候補
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-";

        // 1文字ずつに分解
        $elem = str_split($str);

        // ランダム文字列を生成
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            $rand .= $elem[array_rand($elem, 1)];
        }
        return $rand;
    }

    /**
     *  ランダムな文字列（小文字英数字）を生成する
     *
     *  @access public
     *  @param  string  $len  生成文字列の長さ
     *  @return string  ランダムな文字列
     */
    function getKey($len = 8)
    {
        // 文字列候補
        $str = "abcdefghijklmnopqrstuvwxyz1234567890";

        // 1文字ずつに分解
        $elem = str_split($str);

        // ランダム文字列を生成
        $rand = '';
        for ($i = 0; $i < $len; $i++) {
            if ($i === 0) {
                $rand .= $elem[array_rand($elem, 1) % 26];
            } else {
                $rand .= $elem[array_rand($elem, 1)];
            }
        }
        return $rand;
    }
}
?>
