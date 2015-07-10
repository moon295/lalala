<?php
/**
 *  Admin/Customer/Site/Delete/Confirm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_delete_confirmフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteDeleteConfirm extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_delete_confirm' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  admin_customer_site_delete_confirmアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteDeleteConfirm extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_delete_confirmアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        if ($this->af->validate()) {
            $this->af->setAppNE('error_message', '不正な操作が行われました。');
            return 'admin_customer_site_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_delete_confirmアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $search = array('id' => $this->af->get('action_admin_customer_site_delete_confirm'));
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $siteManager = new SiteManager();
        $data = $siteManager->get($search);
        if (!$data) {
            $this->af->setAppNE('error_message', '選択された情報がありません。');
            return 'admin_customer_site_list';
        }
        $this->af->form_vars = $data;

        // セッション設定
        $this->session->set($this->parentSessionName, $data);

        $this->af->setAppNE('tpl_file', 'admin_customer_site_delete_confirm');
        return 'admin_customer_site_default';
    }
}
?>
