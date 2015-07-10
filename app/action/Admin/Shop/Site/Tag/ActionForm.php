<?php
/**
 *  Admin/Customer/Site/Tag/ActionForm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tagフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagActionForm extends Lpo_ActionForm
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
        'url' => array(
            'name'          => 'URL',
            'max'           => 255,
            'required'      => true,
            'custom'        => 'checkURL,checkURL2,checkDuplication',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'suspend_flg' => array(
            'type'          => VAR_TYPE_INT,
            'form_type'     => FORM_TYPE_CHECKBOX,
            'name'          => '状態',
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
        $url = $this->get('url');
        $this->set('url', 'http://' . $url);
        parent::validate();
        $this->set('url', $url);
        return $this->ae->count();
    }

    /**
     *  重複確認
     *
     *  @access public
     *  @param  string  $name  対象要素名
     */
    function checkDuplication($name)
    {
        $search = array($name => $this->get($name));
        if ($search[$name] !== 'http://') {
            if ($name === 'url') {
                $parseUrl = parse_url($search[$name]);
                $search[$name] = $parseUrl['host'];
            }
            if (strlen($search[$name]) > 0) {
                $search['id'] = $this->backend->session->get($this->backend->ac->parentSessionName . '.id');
                $siteManager = new SiteManager();
                if ($siteManager->isDuplication($search) > 0) {
                    $this->ae->add($name, "{form}はすでに登録されています", E_FORM_INVALIDVALUE);
                }
            }
        }
    }

    /**
     *  URL確認
     *
     *  @access public
     *  @param  string  $name  対象要素名
     */
    function checkURL2($name)
    {
        $url = preg_replace('/\/$/', '', $this->get($name));
        if (preg_match('/^http[s]?:\/\/http[s]?:\/\//', $url)) {
            $this->ae->add($name, "{form}にhttp(https)://は不要です。", E_FORM_INVALIDVALUE);
        } else {
            $parseUrl = parse_url($url);
            if (isset($parseUrl['path'])) {
                $this->ae->add($name, "{form}はファイル名の入力は不要です。", E_FORM_INVALIDVALUE);
            }
        }
    }
}
?>
