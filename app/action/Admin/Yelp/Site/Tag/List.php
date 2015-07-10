<?php
/**
 *  Admin/Customer/Site/Tag/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_listフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminCustomerSiteTagList extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        'action_admin_customer_site_tag_list' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => 'ID',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );

    /**
     *  フォームチェック（オーバーライド）
     *
     *  @access public
     *  @return integer  エラー数
     */
    function validate()
    {
        parent::validate();
        return $this->ae->count();
    }
}

/**
 *  admin_customer_site_tag_listアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminCustomerSiteTagList extends Lpo_ActionClass
{
    /**
     *  admin_customer_site_tag_listアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        if ($this->af->validate()) {
            return 'admin_site_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_listアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        $search = $this->af->getArray(false);
        if (is_null($search['current_page']) || !is_numeric($search['current_page'])) {
            // 検索ボタン押下時
            $this->session->remove($this->sessionName . '.current_page');
            $this->session->set($this->sessionName, $search);
        }

        // 選択したサイト情報取得・設定
        $search = array('id' => $search['action_admin_customer_site_tag_list']);
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $siteManager = new SiteManager();
        $site = $siteManager->get($search);
        if ($site) {
            $this->session->set('admin.site', $site);
        } else {
            $this->af->setAppNE('error_message', 'サイトが存在しません。');
            return 'admin_index';
        }
        return 'admin_customer_site_tag_list';
    }
}
?>
