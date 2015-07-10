<?php
/**
 *  Admin/Index.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_indexフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminIndex extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
    );
}

/**
 *  admin_indexアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminIndex extends Lpo_ActionClass
{
    /**
     * @access protected
     * @var array  $loginCheckFlg  ログインチェックフラグ
     */
    var $loginCheckFlg = true;

    /**
     *  admin_indexアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  admin_indexアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        return 'admin_index';
    }
}
?>
