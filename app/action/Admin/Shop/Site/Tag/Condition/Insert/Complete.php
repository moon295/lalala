<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Insert/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/Site/Tag/Condition/ActionForm.php';

/**
 *  admin_customer_site_tag_condition_insert_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionInsertComplete extends Lpo_Form_AdminCustomerSiteTagConditionActionForm
{
}

/**
 *  admin_customer_site_tag_condition_insert_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionInsertComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_condition_insert_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_tag_condition_list';
        }
        if (!$this->session->get('admin.customer.id')) {
            $this->af->setAppNE('error_message', '顧客が選択されていません。');
            return 'admin_customer_list';
        }
        if (!$this->session->get('admin.site.id')) {
            $this->af->setAppNE('error_message', 'サイトが選択されていません。');
            return 'admin_customer_list';
        }
        if (!$this->session->get('admin.target.id')) {
            $this->af->setAppNE('error_message', '切り替え対象が選択されていません。');
            return 'admin_customer_list';
        }
        $this->_session = $this->session->get($this->parentSessionName);

        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost() || is_null($this->_session)) {
            $this->af->setAppNE('error_message', 'すでに登録処理は完了しています。');
            return 'admin_customer_site_tag_condition_list';
        }
        // 登録前の再チェック
        $this->af->form_vars = $this->_session;
        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_site_tag_condition_insert_input');
            return 'admin_customer_site_default';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_condition_insert_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->_session;
        try {
            $conditionManager = new ConditionManager();
            DB::begin();
            $search = array('target_id' => $this->session->get('admin.target.id'));
            $no = $conditionManager->getNo($search);
            if (!$no) {
                $this->af->setAppNE('error_message', '不正な処理が実行されました。');
                return 'admin_customer_site_tag_conditionlist';
            }
            // 切り替え条件情報登録
            $value = array('#target_id'     => $this->session->get('admin.target.id'),
                           'name'           => $form['name'],
                           '#no'            => $no,
                           '#contents_type' => $form['contents_type'],
                           '#condition_ctg' => $form['condition_ctg'],
                           '#suspend_flg'   => $form['suspend_flg'] ? 1 : 0,
                          );
            // コンテンツ登録
            switch ($form['contents_type']) {
                case 1:
                    // 画像登録
                    DB::formatText('image_url', $form, $value);
                    DB::formatInt('image_target', $form, $value);
                    DB::formatText('image_link_url', $form, $value);
                    break;
                case 2:
                    // HTMLソース登録
                    DB::formatText('html_tag', $form, $value);
                    break;
            }
            // 切り替え条件
            switch ($form['condition_ctg']) {
                case 1:     // リスティング
                    DB::formatInt('listing_site_id', $form, $value, 'site_id');
                    if (!$form['listing_keyword']) {
                        $form['listing_match'] = '';
                    }
                    if (!$form['listing_disable_keyword']) {
                        $form['listing_disable_match'] = '';
                    }
                    DB::formatInt('listing_match', $form, $value, 'enable_match_all_flg');
                    DB::formatText('listing_keyword', $form, $value, 'enable_keywords');
                    DB::formatInt('listing_disable_match', $form, $value, 'disable_match_all_flg');
                    DB::formatText('listing_disable_keyword', $form, $value, 'disable_keywords');
                    break;
                case 2:     // 参照元
                    if (!$form['referrer_url']) {
                        $form['referrer_match'] = '';
                    }
                    if (!$form['referrer_params']) {
                        $form['referrer_params_match'] = '';
                    }
                    DB::formatInt('referrer_match', $form, $value, 'urls_match_all_flg');
                    DB::formatText('referrer_url', $form, $value, 'from_urls');
                    DB::formatInt('referrer_params_match', $form, $value, 'params_match_all_flg');
                    DB::formatText('referrer_params', $form, $value, 'from_params');
                    break;
                case 3:     // キーワード
                    DB::formatInt('keyword_site_id', $form, $value, 'site_id');
                    if (!$form['keyword_keyword']) {
                        $form['keyword_match'] = '';
                    }
                    if (!$form['keyword_disable_keyword']) {
                        $form['keyword_disable_match'] = '';
                    }
                    DB::formatInt('keyword_match', $form, $value, 'enable_match_all_flg');
                    DB::formatText('keyword_keyword', $form, $value, 'enable_keywords');
                    DB::formatInt('keyword_disable_match', $form, $value, 'disable_match_all_flg');
                    DB::formatText('keyword_disable_keyword', $form, $value, 'disable_keywords');
                    break;
                case 4:     // LPパラメーター
                    $value['params_match_all_flg'] = $form['lp_params_match'];
                    $value['from_params']          = $form['lp_params'];
                    break;
            }
            if (!$conditionManager->insert($value)) {
                throw new Exception('データベースの登録に失敗しました。');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_site_tag_condition_list';
        }
        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_site_tag_condition_list';
    }
}
?>
