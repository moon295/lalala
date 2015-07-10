<?php
/**
 *  Admin/Shop/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_shop_listフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminShopList extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        'company_name' => array(
            'name'          => '会社名',
            'max'           => 128,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'mail' => array(
            'name'          => 'メールアドレス',
            'max'           => 255,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'current_page' => array(
            'type'          => VAR_TYPE_INT,
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
 *  admin_shop_listアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminShopList extends Lpo_ActionClass
{
    /**
     *  admin_shop_listアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        if ($this->af->validate()) {
            // 検索ボタン押下時
            $search = $this->af->getArray(false);
            $this->session->remove($this->sessionName . '.current_page');
            $this->session->set($this->sessionName, $search);
            return 'admin_shop_list';
        }
        // 一覧以外の階下のセッション情報初期化
        $this->_session = $this->session->get($this->sessionName);
        $this->session->remove($this->parentSessionName);
        $this->session->set($this->sessionName, $this->_session);
        return null;
    }

    /**
     *  admin_shop_listアクションの実装
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
        return 'admin_shop_list';
    }
}
?>
