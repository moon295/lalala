<?php
// vim: foldmethod=marker
/**
 *  Lpo_Plugin_Validator_Date.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: Lpo_Plugin_Validator_Date.php 312 2006-08-03 03:30:42Z ichii386 $
 */

// {{{ Lpo_Plugin_Validator_Date
/**
 *  customバリデータのラッパープラグイン
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Plugin_Validator_Date extends Lpo_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = true;

    /**
     *  日付バリデータのラッパー
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
            $date = explode('-', $var);
            if (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $var) || !checkdate($date[1], $date[2], $date[0])) {
                return Ethna::raiseNotice('{form}' . 'に正確な日付を選択してください');
            } else {
                if (isset($form_vars['date']['future']) && $form_vars['date']['future']) {
                    if ($var < date('Y-m-d')) {
                        return Ethna::raiseNotice('{form}' . 'に未来の日付を選択してください');
                    }
                }
            }
        } else {
            if (isset($form_vars['date']['required']) && $form_vars['date']['required']) {
                return Ethna::raiseNotice('{form}' . 'を選択してください');
            }
        }
        return $true;
    }
}
// }}}
?>
