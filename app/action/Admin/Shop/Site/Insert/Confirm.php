<?php
/**
 *  Admin/Customer/Site/Insert/Confirm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/Site/ActionForm.php';

/**
 *  admin_customer_site_insert_confirmフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteInsertConfirm extends Lpo_Form_AdminCustomerSiteActionForm
{
}

/**
 *  admin_customer_site_insert_confirmアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteInsertConfirm extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_insert_confirmアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        $this->_session = $this->session->get($this->parentSessionName);

        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_site_insert_input');
            return 'admin_customer_site_default';
        }
        return null;
    }

    /**
     *  admin_customer_site_insert_confirmアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $this->session->set($this->parentSessionName, $this->af->getArray(false));
        $this->af->setAppNE('tpl_file', 'admin_customer_site_insert_confirm');
        return 'admin_customer_site_default';
    }
}
?>
