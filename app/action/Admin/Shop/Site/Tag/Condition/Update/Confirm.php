<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Update/Confirm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/Site/Tag/Condition/ActionForm.php';

/**
 *  admin_customer_site_tag_condition_update_confirmフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionUpdateConfirm extends Lpo_Form_AdminCustomerSiteTagConditionActionForm
{
}

/**
 *  admin_customer_site_tag_condition_update_confirmアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionUpdateConfirm extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_condition_update_confirmアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        $this->_session = $this->session->get($this->parentSessionName);

        // IDが違う場合
        if ($this->af->get('id') != $this->_session['id']) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_tag_condition_list';
        }

        if ($this->af->validate()) {
            $form = $this->af->getArray(false);
            $data = array_merge($this->_session, $form);
            $this->af->form_vars = $data;
            $this->af->setAppNE('tpl_file', 'admin_customer_site_tag_condition_update_input');
            return 'admin_customer_site_tag_condition_default';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_condition_update_confirmアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        $data = array_merge($this->_session, $form);
        $this->af->form_vars = $data;
        $this->session->set($this->parentSessionName, $data);
        $this->af->setAppNE('tpl_file', 'admin_customer_site_tag_condition_update_confirm');
        return 'admin_customer_site_tag_condition_default';
    }
}
?>
