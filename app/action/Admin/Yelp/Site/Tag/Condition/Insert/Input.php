<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Insert/Input.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_insert_inputフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionInsertInput extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_tag_condition_insert_input' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'ID',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
     );
}

/**
 *  admin_customer_site_tag_condition_insert_inputアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionInsertInput extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_condition_insert_inputアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        Ethna_Util::isDuplicatePost();
        if (Ethna_Util::isDuplicatePost()) {
            $this->_session = $this->session->get($this->parentSessionName);
            if (!is_null($this->_session)) {
                $this->af->form_vars = $this->_session;
            }
        } else {
            $this->session->remove($this->parentSessionName);
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_condition_insert_inputアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        if (is_numeric($this->af->get('action_admin_customer_site_tag_condition_insert_input'))) {
            // 子会員の登録時
            $this->af->set('team_id', $this->af->get('action_admin_customer_site_tag_condition_insert_input'));
        }
        $this->af->setAppNE('tpl_file', 'admin_customer_site_tag_condition_insert_input');
        return 'admin_customer_site_default';
    }
}
?>
