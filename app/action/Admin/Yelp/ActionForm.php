<?php
/**
 *  Admin/Customer/ActionForm.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customerフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerActionForm extends Lpo_ActionForm
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
        'company_name' => array(
            'name'          => '会社名',
            'max'           => 50,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'name_sei' => array(
            'name'          => '担当者（姓）',
            'max'           => 32,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'name_mei' => array(
            'name'          => '担当者（名）',
            'max'           => 32,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'mail' => array(
            'name'          => 'メールアドレス',
            'max'           => 255,
            'custom'        => 'checkMailAddress,checkDuplication',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'password' => array(
            'name'          => 'パスワード',
            'max'           => 4,
            'max'           => 16,
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
        parent::validate();
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
        if (strlen($search[$name]) > 0) {
            $search['id'] = $this->backend->session->get($this->backend->ac->parentSessionName . '.id');
            $customerManager = new CustomerManager();
            if ($customerManager->isDuplication($search) > 0) {
                $this->ae->add($name, "{form}はすでに登録されています", E_FORM_INVALIDVALUE);
            }
        }
    }
}
?>
