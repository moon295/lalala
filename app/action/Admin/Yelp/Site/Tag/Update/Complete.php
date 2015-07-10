<?php
/**
 *  Admin/Customer/Site/Tag/Update/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_update_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagUpdateComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'name' => array(
            'name'          => '名前',
            'max'           => 32,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'action_admin_customer_site_tag_update_complete' => array(
            'type'          => VAR_TYPE_INT,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  admin_customer_site_tag_update_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagUpdateComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_update_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            $this->af->clearFormVars();
            return 'admin_customer_site_tag_list';
        }
        $this->_session = $this->session->get($this->parentSessionName);
        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost() || is_null($this->_session)) {
            $this->af->setAppNE('error_message', 'すでに更新処理は完了しています。');
            return 'admin_customer_site_tag_list';
        }
        // IDが違う場合
        if ($this->af->get('action_admin_customer_site_tag_update_complete') != $this->_session['id']) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_tag_list';
        }
        if ($this->af->validate()) {
            return 'admin_customer_site_tag_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_update_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        $targetManager = new TargetManager();
        $value = array('name'         => $form['name'],
                       '#update_time' => 'NOW()',
                      );
        $where = array('id' => $form['action_admin_customer_site_tag_update_complete']);
        if ($targetManager->update($value, $where) === -1) {
            $this->af->setAppNE('error_message', 'データベースの削除に失敗しました。');
        }
            $this->af->clearFormVars();
        return 'admin_customer_site_tag_list';
    }
}
?>
