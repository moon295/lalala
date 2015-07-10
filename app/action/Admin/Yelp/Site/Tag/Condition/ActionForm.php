<?php
/**
 *  Admin/Customer/Site/Tag/Condition/ActionForm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_conditionフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionActionForm extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'id' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'ID',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'name' => array(
            'name'          => 'サイト名',
            'max'           => 128,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'suspend_flg' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '停止状況',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'contents_type' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '種別',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'image_url' => array(
            'name'          => '切り替え画像URL',
            'max'           => 255,
            'custom'        => 'checkURL',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'image_target' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '切り替え画像のリンク設定の有無',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'image_link_url' => array(
            'name'          => '切り替え画像のリンク有りの場合のリンク先URL',
            'max'           => 255,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'html_tag' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'HTMLソース',
            'max'           => 5000,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'condition_ctg' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '切り替え条件',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'listing_site_id' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => 'サイト',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'listing_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'listing_keyword' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'キーワード',
            'max'           => 5000,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'listing_disable_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '拒否一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'listing_disable_keyword' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => '拒否キーワード',
            'max'           => 5000,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'referrer_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => 'URL一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'referrer_url' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'URL',
            'max'           => 25500,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'referrer_params_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => 'パラメーター一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'referrer_params' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'パラメーター',
            'max'           => 25500,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'lp_params_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => 'パラメーター一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'lp_params' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'パラメーター',
            'max'           => 25500,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'keyword_site_id' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => 'サイト',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'keyword_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'keyword_keyword' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => 'キーワード',
            'max'           => 5000,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'keyword_disable_match' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_RADIO,
            'name'          => '拒否一致条件',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'keyword_disable_keyword' => array(
            'form_type'     => FORM_TYPE_TEXTAREA,
            'name'          => '拒否キーワード',
            'max'           => 5000,
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
        $form = $this->getArray(false);
        // 切り替え素材
        switch ($form['contents_type']) {
            case 1:     // 画像登録
                $this->form['image_url']['required']    = true;
                $this->form['image_target']['required'] = true;
                if ($form['image_target'] != 1) {
                    $this->form['image_link_url']['required'] = true;
                }
                unset($this->form['html_tag']);
                break;
            case 2:     // HTMLソース登録
                $this->form['html_tag']['required'] = true;
                unset($this->form['image_url']);
                unset($this->form['image_target']);
                unset($this->form['image_link_url']);
                break;
        }
        // 切り替え条件
        switch ($form['condition_ctg']) {
            case 1:     // リスティング
                $this->form['listing_site_id']['required'] = true;
                if ($form['listing_keyword']) {
                    $this->form['listing_match']['required'] = true;
                }
                if ($form['listing_disable_keyword']) {
                    $this->form['listing_disable_match']['required'] = true;
                }
                unset($this->form['referrer_match']);
                unset($this->form['referrer_url']);
                unset($this->form['referrer_params_match']);
                unset($this->form['referrer_params']);
                unset($this->form['keyword_site_id']);
                unset($this->form['keyword_match']);
                unset($this->form['keyword_keyword']);
                break;
            case 2:     // 参照元
                if ($form['referrer_url']) {
                    $this->form['referrer_match']['required'] = true;
                }
                if ($form['referrer_params']) {
                    $this->form['referrer_params_match']['required'] = true;
                }
                unset($this->form['keyword_site_id']);
                unset($this->form['keyword_match']);
                unset($this->form['keyword_keyword']);
                unset($this->form['listing_site_id']);
                unset($this->form['listing_match']);
                unset($this->form['listing_keyword']);
                break;
            case 3:     // キーワード
                $this->form['keyword_site_id']['required'] = true;
                if ($form['keyword_keyword']) {
                    $this->form['keyword_match']['required'] = true;
                }
                if ($form['keyword_disable_keyword']) {
                    $this->form['keyword_disable_match']['required'] = true;
                }
                unset($this->form['listing_site_id']);
                unset($this->form['listing_match']);
                unset($this->form['listing_keyword']);
                unset($this->form['referrer_match']);
                unset($this->form['referrer_url']);
                unset($this->form['referrer_params_match']);
                unset($this->form['referrer_params']);
                break;
            case 4:     // LPパラメーター
                $this->form['lp_params']['required']       = true;
                $this->form['lp_params_match']['required'] = true;
                unset($this->form['keyword_site_id']);
                unset($this->form['keyword_match']);
                unset($this->form['keyword_keyword']);
                unset($this->form['listing_site_id']);
                unset($this->form['listing_match']);
                unset($this->form['listing_keyword']);
                break;
        }
        parent::validate();
        // 切り替え画像のリンク有りの場合のリンク先URL
        if (!$this->ae->getMessage('image_link_url')) {
            // 電話番号リンクの場合、URLチェックをスキップする
            if (!preg_match('/^tel:[0-9]+$/', $form['image_link_url'])) {
                $this->checkURL('image_link_url');
            }
        }
        // 切り替え素材
        if ($form['contents_type'] == 2) {
            if (preg_match('/<[\\/]?script|<[\\/]?pre/', $form['html_tag'])) {
                $this->ae->add('html_tag', '{form}に使用できない文字が含まれています', E_FORM_INVALIDVALUE);
            }
        }
        // キーワード確認
        if ($form['condition_ctg'] == 1 && !$form['listing_keyword'] && !$form['listing_disable_keyword']) {
            $this->ae->add('listing_keyword', 'キーワードか拒否キーワードのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
            $this->ae->add('listing_disable_keyword', 'キーワードか拒否キーワードのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
        } else if ($form['condition_ctg'] == 2 && !$form['referrer_url'] && !$form['referrer_params']) {
            $this->ae->add('referrer_url', 'URLかパラメーターのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
            $this->ae->add('referrer_params', 'URLかパラメーターのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
        } else if ($form['condition_ctg'] == 3 && !$form['keyword_keyword'] && !$form['keyword_disable_keyword']) {
            $this->ae->add('keyword_keyword', 'キーワードか拒否キーワードのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
            $this->ae->add('keyword_disable_keyword', 'キーワードか拒否キーワードのいずれかを入力して下さい', E_FORM_INVALIDVALUE);
        }
        // キーワード、URL確認
        switch ($form['condition_ctg']) {
            case 1:     // リスティング
            case 3:     // キーワード
                if (!$this->ae->getMessage('listing_keyword') && !$this->ae->getMessage('keyword_keyword')) {
                    $names = array('listing_keyword', 'listing_disable_keyword');
                    if ($form['condition_ctg'] == 3) {
                        $names = array('keyword_keyword', 'keyword_disable_keyword');
                    }
                    foreach ($names as $name) {
                        if (preg_match('/[<>"\',]+/', $form[$name])) {
                            $this->ae->add($name, '{form}では< 　"　\'　 ,　 >　は使用できません', E_FORM_INVALIDVALUE);
                        } else {
                            $errMsg = array();
                            $keyword = explode("\n", $form[$name]);
                            if (count($keyword) > 100) {
                                $this->ae->add($name, '{form}は100キーワード以内で入力して下さい', E_FORM_INVALIDVALUE);
                            } else {
                                foreach ($keyword as $key => $value) {
                                    if (mb_strlen($value, 'UTF-8') > 50) {
                                        $errMsg[] = sprintf("%d行目のキーワードは50文字以内で入力して下さい", $key + 1);
                                    }
                                }
                                if ($errMsg) {
                                    $this->ae->add($name, implode('<br>', $errMsg), E_FORM_INVALIDVALUE);
                                }
                            }
                        }
                    }
                }
                break;
            case 2:     // 参照元
                if (!$this->ae->getMessage('referrer_url')) {
                    $errMsg = array();
                    $keyword = explode("\n", $form['referrer_url']);
                    if (count($keyword) > 100) {
                        $this->ae->add('referrer_url', '{form}は100URL以内で入力して下さい', E_FORM_INVALIDVALUE);
                    } else {
                        foreach ($keyword as $key => $value) {
                            $value = trim($value);
                            if ($value) {
                                if (mb_strlen($value, 'UTF-8') > 200) {
                                    $errMsg[] = sprintf("%d行目のURLは200文字以内で入力して下さい", $key + 1);
                                } else if (preg_match('/^(http:\/\/|https:\/\/|ftp:\/\/)/', $value) == 0) {
                                    $errMsg[] = sprintf("%d行目のURLを正しく入力して下さい", $key + 1);
                                }
                            }
                        }
                        if ($errMsg) {
                            $this->ae->add('referrer_url', implode('<br>', $errMsg), E_FORM_INVALIDVALUE);
                        }
                    }
                }
                break;
        }
        return $this->ae->count();
    }
}
?>
