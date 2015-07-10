<?php
/**
 *  Admin/Bill/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_bill_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminBillList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        $billManager = new BillManager();

        // 締め日セレクトボックス
        $closeDateList = array();
        $list = $billManager->getCloseDateList();
        foreach ($list as $value) {
            $closeDateList[$value['close_date']] = date('Y年m月', strtotime($value['close_date']));
        }

        // ページャ遷移時
        $search = $this->af->getArray(false);
        if (!$search['close_date']) {
            $search['close_date'] = current(array_flip($closeDateList));
        }
        // 管理者ID
        $search['administrator_id'] = $this->session->get('admin.login.id');
        $search['orderby']      = array('id ASC');
        $list = $billManager->getList($search);
        $customerList = array();
        foreach ($list as $key => $value) {
            if (!isset($customerList[$value['customer_id']])) {
                $value['use_date']     = date('Y年m月', strtotime($value['close_date']));
                $value['total_amount'] = 0;
                $customerList[$value['customer_id']] = $value;
            }
            $customerList[$value['customer_id']]['total_amount'] += $value['basic_charge'] + $value['call_charges'];
        }
        $this->af->setAppNE('list', $customerList);
        $this->af->setApp('close_date_list', $closeDateList);
    }
}
?>
