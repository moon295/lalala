<?php
/**
 *  Admin/Customer/Site/Update/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/Site/ActionForm.php';

/**
 *  admin_customer_site_update_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteUpdateComplete extends Lpo_Form_AdminCustomerSiteActionForm
{
}

/**
 *  admin_customer_site_update_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteUpdateComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_update_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_list';
        }

        $this->_session = $this->session->get($this->parentSessionName);

        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost() || is_null($this->_session)) {
            $this->af->setAppNE('error_message', 'すでに更新処理は完了しています。');
            return 'admin_customer_site_list';
        }
        // IDが違う場合
        if ($this->af->get('id') != $this->_session['id']) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_list';
        }
        // 更新前の再チェック
        $this->af->form_vars = $this->_session;
        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_site_update_input');
            return 'admin_customer_site_default';
        }
        return null;
    }

    /**
     *  admin_customer_site_update_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->_session;
        $parseUrl = parse_url('http://' . $form['url']);
        $value = array('name'         => $form['name'],
                       'url'          => $parseUrl['host'],
                       '#suspend_flg' => $form['suspend_flg'] ? 1 : 0,
                       '#update_time' => 'NOW()',
                      );
        // 更新
        $where['id'] = $form['id'];
        $siteManager = new SiteManager();
        if ($siteManager->update($value, $where) === -1) {
            $this->af->setAppNE('error_message', 'データベースの更新に失敗しました。');
            return 'admin_customer_site_list';
        }

        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_site_list';
    }
}
?>
