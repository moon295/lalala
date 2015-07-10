<?php
// vim: foldmethod=marker
/**
 *  Lpo_ActionForm.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.actionform.php 323 2006-08-22 15:52:26Z fujimoto $
 */

// {{{ Lpo_ActionForm
/**
 *  アクションフォームクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_ActionForm extends Ethna_ActionForm
{
    /**#@+
     *  @access private
     */

    /** @var    array   フォーム値定義(デフォルト) */
    var $form_template = array();

    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = true;

    /**#@-*/

    /**
     *  Lpo_ActionFormクラスのコンストラクタ
     *
     *  @access public
     *  @param  object  Ethna_Controller    &$controller    controllerオブジェクト
     */
    function Lpo_ActionForm(&$controller)
    {
        // 省略値補正
        foreach ($this->form as $name => $value) {
            if (!isset($this->form[$name]['type'])) {
                $this->form[$name]['type'] = VAR_TYPE_STRING;
            }
            if (!isset($this->form[$name]['form_type'])) {
                $this->form[$name]['form_type'] = FORM_TYPE_TEXT;
            }
            if (!isset($this->form[$name]['name'])) {
                $this->form[$name]['name'] = '';
            }
            if (!isset($this->form[$name]['required'])) {
                $this->form[$name]['required'] = false;
            }
            if (!isset($this->form[$name]['min'])) {
                $this->form[$name]['min'] = null;
            }
            if (!isset($this->form[$name]['max'])) {
                $this->form[$name]['max'] = null;
            }
            if (!isset($this->form[$name]['regexp'])) {
                $this->form[$name]['regexp'] = null;
            }
            if (!isset($this->form[$name]['custom'])) {
                $this->form[$name]['custom'] = null;
            }
            if (!isset($this->form[$name]['filter'])) {
                $this->form[$name]['filter'] = null;
            }
        }
        parent::Ethna_ActionForm($controller);
    }

    /**
     *  Error handling of form input validation.
     *
     *  @access public
     *  @param  string      $name   form item name.
     *  @param  int         $code   error code.
     */
    function handleError($name, $code)
    {
        return parent::handleError($name, $code);
    }

    /**
     *  setter method for form template.
     *
     *  @access protected
     *  @param  array   $form_template  form template
     *  @return array   form template after setting.
     */
    function _setFormTemplate($form_template)
    {
        return parent::_setFormTemplate($form_template);
    }

    /**
     *  setter method for form definition.
     *
     *  @access protected
     */
    function _setFormDef()
    {
        return parent::_setFormDef();
    }

    /**
     * 全角カナチェックを行います。
     *
     * @access public
     * @param string $name 入力フォーム値
     * @return null
     */
    function &checkZenkana($name)
    {
        $null = null;
        $form_vars = $this->check($name);

        if ($form_vars == null) {
            return $null;
        }

        foreach ($form_vars as $v) {
            if ($v === '') {
                continue;
            }
            if (!ereg("^(\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xB6]|\xE3\x83\xBC|\xE3\x83\xBB|\xE3\x80\x80)+$", $v)) {
                return $this->ae->add($name, '{form}は全角カタカナで入力して下さい', E_FORM_INVALIDCHAR);
            }
        }

        return $null;
    }

    /**
     * 郵便番号チェックを行います。
     *
     * @access public
     * @param string $name 入力フォーム値
     * @return null
     */
    function &checkZip($name)
    {
        $null = null;
        $form_vars = $this->check($name);

        if ($form_vars == null) {
            return $null;
        }

        foreach ($form_vars as $v) {
            if ($v === '') {
                continue;
            }
            if (!preg_match("/^\d{3}\d{4}$/", $v) && !preg_match("/^\d{3}-\d{4}$/", $v) ) {
                return $this->ae->add($name, '{form}を正しく入力して下さい', E_FORM_INVALIDCHAR);
            }
        }

        return $null;
    }

    /**
     * 電話番号チェックを行います。
     *
     * @access public
     * @param string $name 入力フォーム値
     * @return null
     */
    function &checkTel($name)
    {
        $null = null;
        $form_vars = $this->check($name);

        if ($form_vars == null) {
            return $null;
        }

        foreach ($form_vars as $v) {
            if ($v === '') {
                continue;
            }
            if (!preg_match("/^0\d{1,4}?\d{1,4}?\d{3,4}$/", $v) && !preg_match("/^0\d{4}?\d{3,4}?$/", $v) && !preg_match("/^0120\d{1,5}?\d{1,5}$/", $v)) {
                return $this->ae->add($name, '{form}を正しく入力して下さい', E_FORM_INVALIDCHAR);
            }
        }

        return $null;
    }

    /**
     * コンフィグ設定値チェックを行います。
     *
     * @access public
     * @param string $name       入力フォーム値
     * @param string $configName チェック項目名
     * @return null
     */
    function &checkConfigValue($name, $configName)
    {
        if ($this->ae->isError($name)) {
            return null;
        }
        $form_vars = $this->check($name);

        if ($form_vars == null) {
            return $null;
        }

        $config = $this->backend->config->config;
        foreach ($form_vars as $v) {
            if ($v === '') {
                continue;
            }
            if (!isset($config[$configName][$v])) {
                return $this->ae->add($name, '{form}を正しく設定して下さい', E_FORM_INVALIDCHAR);
            }
        }

        return $null;
    }

    /**
     *  フォーム値変換フィルタ: 全角英数字->半角英数字
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_alnum_zentohan($value)
    {
        $org = array('’',     //シングルクォーテーション
                     '”',     //ダブルクォーテーション
                     '￥',     //円マーク
                    );
        $new = array(chr(hexdec("27")),
                     chr(hexdec("22")),
                     chr(hexdec("5C")),
                    );
        $value = str_replace($org, $new, $value);
        return mb_convert_kana($value, "a");
    }

    /**
     *  フォーム値変換フィルタ: 半角カナ->全角カナ
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_kana_hantozen($value)
    {
        return mb_convert_kana($value, "KV");
    }

    /**
     *  配列フォーム値変換フィルタ: 全角英数字->半角英数字
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_alnum_zentohan($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_alnum_zentohan($val);
            }
        } else {
            return self::_filter_alnum_zentohan($value);
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: 全角数字->半角数字
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_numeric_zentohan($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_numeric_zentohan($val);
            }
        } else {
            return mb_convert_kana($value, "n");
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: 全角英字->半角英字
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_alphabet_zentohan($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_alphabet_zentohan($val);
            }
        } else {
            return mb_convert_kana($value, "r");
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: 左空白削除
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_ltrim($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_ltrim($val);
            }
        } else {
            return ltrim($value);
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: 右空白削除
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_rtrim($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_rtrim($val);
            }
        } else {
            return rtrim($value);
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: NULL(0x00)削除
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_ntrim($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_ntrim($val);
            }
        } else {
            return str_replace("\x00", '', $value);
        }
        return $value;
    }

    /**
     *  配列フォーム値変換フィルタ: 半角カナ->全角カナ
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_array_kana_hantozen($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = self::_filter_array_kana_hantozen($val);
            }
        } else {
            return mb_convert_kana($value, "KV");
        }
        return $value;
    }

    /**
     *  フォーム値変換フィルタ: 半角カナ->全角カナ（オーバーライド）
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
/*
    function _filter_kana_hantozen($value)
    {
        return mb_convert_kana($value, "KVS");
    }
*/
}
// }}}
?>
