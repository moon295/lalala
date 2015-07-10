<?php
// vim: foldmethod=marker
/**
 *  Lpo_Plugin_Validator_Date.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: Lpo_Plugin_Validator_Time.php 312 2006-08-03 03:30:42Z ichii386 $
 */

// {{{ Lpo_Plugin_Validator_Time
/**
 *  customバリデータのラッパープラグイン
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Plugin_Validator_Time extends Lpo_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = true;

    /**
     *  時間バリデータのラッパー
     *
     *  @access public
     *  @param  string  $name       フォームの名前
     *  @param  mixed   $var        フォームの値
     *  @param  array   $params     プラグインのパラメータ
     */
    function &validate($name, $var, $params)
    {
        $true = true;
        $false = false;
        $form_vars = $this->getFormDef($name);
        if ($form_vars == null) {
            return $null;
        }
        if ($var) {
            $time = explode(':', $var);
            if (!((int)$time[0] >= 0 && (int)$time[0] <= 23 && (int)$time[1] >= 0 && (int)$time[1] <= 59)) {
                return Ethna::raiseNotice('{form}' . 'に正確な時間を選択してください');
            } else {
                if (isset($time[2]) && !((int)$time[2] >= 0 && (int)$time[2] <= 59)) {
                    return Ethna::raiseNotice('{form}' . 'に正確な時間を選択してください');
                }
            }
        } else {
            if (isset($form_vars['time']['required']) && $form_vars['time']['required']) {
                return Ethna::raiseNotice('{form}' . 'を選択してください');
            }
        }
        return $true;
    }
}
// }}}
?>
