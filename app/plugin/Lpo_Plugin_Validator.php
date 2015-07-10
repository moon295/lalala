<?php
// vim: foldmethod=marker
/**
 *  Lpo_Plugin_Validator.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: Ethna_Plugin_Validator.php 298 2006-07-19 05:22:39Z fujimoto $
 */

require_once 'Ethna/class/Plugin/Ethna_Plugin_Validator.php';

// UPLOAD_ERR_* が未定義の場合 (PHP 4.3.0 以前)
if (defined('UPLOAD_ERR_OK') == false) {
    define('UPLOAD_ERR_OK', 0);
}

// {{{ Lpo_Plugin_Validator
/**
 *  バリデータプラグインの基底クラス
 *  
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Plugin_Validator extends Ethna_Plugin_Validator
{
    /**#@+
     *  @access private
     */

    /** @var    object  Ethna_Backend   backendオブジェクト */
    var $backend;

    /** @var    object  Ethna_Logger    ログオブジェクト */
    var $logger;

    /** @var    object  Ethna_ActionForm    フォームオブジェクト */
    var $action_form;

    /** @var    object  Ethna_ActionForm    フォームオブジェクト */
    var $af;

    /** @var    bool    配列を受け取るバリデータかどうかのフラグ */
    var $accept_array = false;

    /**#@-*/

    /**
     *  コンストラクタ
     *
     *  @access public
     *  @param  object  Ethna_Controller    $controller コントローラオブジェクト
     */
    function Lpo_Plugin_Validator(&$controller)
    {
        parent::Ethna_Plugin_Validator($controller);

        // 省略値補正
        foreach ($this->action_form->form as $name => $value) {

            if (!isset($this->action_form->form[$name]['type'])) {
                $this->action_form->form[$name]['type'] = VAR_TYPE_STRING;
            }
            if (!isset($this->action_form->form[$name]['form_type'])) {
                $this->action_form->form[$name]['form_type'] = FORM_TYPE_TEXT;
            }
            if (!isset($this->action_form->form[$name]['name'])) {
                $this->action_form->form[$name]['name'] = '';
            }
            if (!isset($this->action_form->form[$name]['required'])) {
                $this->action_form->form[$name]['required'] = false;
            }
            if (!isset($this->action_form->form[$name]['min'])) {
                $this->action_form->form[$name]['min'] = null;
            }
            if (!isset($this->action_form->form[$name]['max'])) {
                $this->action_form->form[$name]['max'] = null;
            }
            if (!isset($this->action_form->form[$name]['regexp'])) {
                $this->action_form->form[$name]['regexp'] = null;
            }
            if (!isset($this->action_form->form[$name]['filter'])) {
                $this->action_form->form[$name]['filter'] = null;
            }
        }
    }
}
// }}}
?>
