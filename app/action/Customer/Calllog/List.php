<?php
/**
 *  Customer/Calllog/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  customer_calllog_listフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_CustomerCalllogList extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        's_advertiser_id' => array(
            'name'          => '広告主ID',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        's_search_period' => array(
            'name'          => '検索期間',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        's_start_date' => array(
            'name'          => '日付（開始）',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
            'plugin'        => true,
            'date'          => array('required' => false, 'future' => false),
        ),
        's_end_date' => array(
            'name'          => '日付（終了）',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
            'plugin'        => true,
            'date'          => array('required' => false, 'future' => false),
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
 *  customer_calllog_listアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_CustomerCalllogList extends Lpo_ActionClass
{
    /**
     *  customer_calllog_listアクションの前処理
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
            return 'customer_calllog_list';
        }
        // 一覧以外の階下のセッション情報初期化
        $this->_session = $this->session->get($this->sessionName);
        $this->session->remove($this->parentSessionName);
        $this->session->set($this->sessionName, $this->_session);
        return null;
    }

    /**
     *  customer_calllog_listアクションの実装
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
        return 'customer_calllog_list';
    }
}
?>
