<?php
/**
 *  smarty_function:has_error()
 *
 *  @param  array   $params  引数
 *  @param  string  $smarty  Smartyオブジェクト
 *  @return string  フォーマット済み文字列
 */
function smarty_function_has_error($params, &$smarty)
{
    if (isset($params['name']) === false) {
        return '';
    }

    $names = explode(',', $params['name']);

    $c =& Ethna_Controller::getInstance();
    $action_error =& $c->getActionError();

    $errorFlg = false;
    foreach ($names as $name) {
        if ($action_error->getMessage($name)) {
            $errorFlg = true;
        }
    }
    return $errorFlg ? ' has-error' : '';
}
?>
