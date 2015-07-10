<?php
/**
 *  Admin/Customer/Site/Update/Input.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_update_inputフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteUpdateInput extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_update_input' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  admin_customer_site_update_inputアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteUpdateInput extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_update_inputアクションの前処理
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
            if ($this->af->validate()) {
                $this->af->setAppNE('error_message', '不正な操作が行われました。');
                return 'admin_customer_site_list';
            }
            $this->session->remove($this->parentSessionName);
        }
        return null;
    }

    /**
     *  admin_customer_site_update_inputアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        if (!$this->_session) {
            $search = array('id' => $this->af->get('action_admin_customer_site_update_input'));
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
        }
        $this->af->setAppNE('tpl_file', 'admin_customer_site_update_input');
        return 'admin_customer_site_default';
    }
}
?>
