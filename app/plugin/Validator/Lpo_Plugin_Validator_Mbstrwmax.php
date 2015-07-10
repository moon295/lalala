<?php
// vim: foldmethod=marker
/**
 *  Lpo_Plugin_Validator_Mbstrwmax.php
 *
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: f584ea5c0264ef8fc24d085c3e960031ad68bb9f $
 */

// {{{ Lpo_Plugin_Validator_Mbstrwmax
/**
 *  最大値チェックプラグイン (マルチバイト文字列用)
 *
 *  NOTE:
 *      - mbstring を有効にしておく必要があります。
 *      - エラーメッセージは、全角半角を区別しません。
 * 
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Ethna
 */
class Lpo_Plugin_Validator_Mbstrwmax extends Ethna_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = false;

    /**
     *  最大値のチェックを行う
     *
     *  @access public
     *  @param  string  $name       フォームの名前
     *  @param  mixed   $var        フォームの値
     *  @param  array   $params     プラグインのパラメータ
     *  @return true: 成功  Ethna_Error: エラー
     */
    function &validate($name, $var, $params)
    {
        $true = true;
        $type = $this->getFormType($name);
        if (isset($params['mbstrwmax']) == false || $this->isEmpty($var, $type)) {
            return $true;
        }

        if ($type == VAR_TYPE_STRING) {
            $max_param = $params['mbstrwmax'];
            if (mb_strwidth($var, 'UTF-8') > $max_param) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = _et('Please input less than %d full-size (%d half-size) characters to {form}.');
                }
                return Ethna::raiseNotice($msg, E_FORM_MAX_STRING,
                        array(intval($max_param/2), $max_param));
            }
        }

        return $true;
    }
}
// }}}

?>
