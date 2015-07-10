<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Delete/Confirm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_delete_confirmフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionDeleteConfirm extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_tag_condition_delete_confirm' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  action_admin_customer_site_tag_condition_delete_confirmアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionDeleteConfirm extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  action_admin_customer_site_tag_condition_delete_confirmアクションの前処理
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
     *  action_admin_customer_site_tag_condition_delete_confirmアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $search = array('id' => $this->af->get('action_admin_customer_site_tag_condition_delete_confirm'));
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $conditionManager = new ConditionManager();
        $data = $conditionManager->get($search);
        if (!$data) {
            $this->af->setAppNE('error_message', '選択された情報がありません。');
            return 'admin_customer_site_tag_condition_list';
        }
        switch ($data['condition_ctg']) {
            case 1:     // リスティング
                $data['listing_site_id']         = $data['site_id'];
                $data['listing_match']           = $data['enable_match_all_flg'];
                $data['listing_keyword']         = $data['enable_keywords'];
                $data['listing_disable_match']   = $data['disable_match_all_flg'];
                $data['listing_disable_keyword'] = $data['disable_keywords'];
                break;
            case 2:     // 参照元
                $data['referrer_match']        = $data['urls_match_all_flg'];
                $data['referrer_url']          = $data['from_urls'];
                $data['referrer_params_match'] = $data['params_match_all_flg'];
                $data['referrer_params']       = $data['from_params'];
                break;
            case 3:     // キーワード
                $data['keyword_site_id']         = $data['site_id'];
                $data['keyword_match']           = $data['enable_match_all_flg'];
                $data['keyword_keyword']         = $data['enable_keywords'];
                $data['keyword_disable_match']   = $data['disable_match_all_flg'];
                $data['keyword_disable_keyword'] = $data['disable_keywords'];
                break;
            case 4:     // LPパラメーター
                $data['lp_params_match'] = $data['params_match_all_flg'];
                $data['lp_params']       = $data['from_params'];
                break;
        }
        $this->af->form_vars = $data;

        // セッション設定
        $this->session->set($this->parentSessionName, $data);

        $this->af->setAppNE('tpl_file', 'admin_customer_site_tag_condition_delete_confirm');
        return 'admin_customer_site_tag_condition_default';
    }
}
?>
