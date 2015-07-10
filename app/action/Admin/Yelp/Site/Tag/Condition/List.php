<?php
/**
 *  Admin/Customer/Site/Tag/Condition/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_listフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminCustomerSiteTagConditionList extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        'action_admin_customer_site_tag_condition_list' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => 'ID',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'period' => array(
            'name'          => '期間',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'sort' => array(
            'name'          => '並び順',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'current_page' => array(
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
 *  admin_customer_site_tag_condition_listアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminCustomerSiteTagConditionList extends Lpo_ActionClass
{
    /**
     *  admin_customer_site_tag_condition_listアクションの前処理
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
     *  admin_customer_site_tag_condition_listアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        $search = $this->af->getArray(false);
        if (is_null($search['current_page']) || !is_numeric($search['current_page'])) {
            if ($search['sort']) {
                $this->session->remove($this->sessionName . '.current_page');
            } else {
                // 検索ボタン押下時
                $this->session->remove($this->sessionName . '.current_page');
                $this->session->set($this->sessionName, $search);
            }
        }
        $this->session->set($this->sessionName . '.sort', $search['sort'] ? $search['sort'] : 'no_asc');

        // 選択した切り替え対象情報取得・設定
        $search = array('id' => $search['action_admin_customer_site_tag_condition_list']);
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $targetManager = new TargetManager();
        $target = $targetManager->get($search);
        if ($target) {
            $target['tag1'] = sprintf('<div id="%s"></div>', $target['key']);
            $this->session->set('admin.target', $target);
        } else {
            $this->af->setAppNE('error_message', 'サイトが存在しません。');
            return 'admin_index';
        }
        return 'admin_customer_site_tag_condition_list';
    }
}
?>
