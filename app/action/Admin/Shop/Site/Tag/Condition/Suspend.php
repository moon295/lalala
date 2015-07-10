<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Suspend.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_suspendフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionSuspend extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'id' => array(
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'type' => array(
            'name'          => '種別',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  admin_customer_site_tag_condition_suspendアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionSuspend extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_condition_suspendアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        if ($this->af->validate()) {
            $json = array('code' => 'error');
            $this->outputJson($json);
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_condition_suspendアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        $id = $this->af->get('id');
        $json = array('code' => 'error');
        $conditionManager = new ConditionManager();
        $value = array('#suspend_flg' => $form['type'] === 'on' ? 1 : 0,
                       '#update_time' => 'NOW()',
                      );
        $where = array('id' => $form['id']);
        if ($conditionManager->update($value, $where) > 0) {
            $json['code'] = 'success';
            $json['id']   = $form['id'];
        }
        $this->outputJson($json);
    }
}
?>
