<?php
/**
 *  smarty_function:msg()
 *
 *  @param  array   $params  引数
 *  @param  string  $smarty  Smartyオブジェクト
 *  @return string  フォーマット済み文字列
 */
function smarty_function_msg($params, &$smarty)
{
    if (isset($params['name']) === false) {
        return '';
    }

    $c =& Ethna_Controller::getInstance();
    $action_error =& $c->getActionError();

    $message = $action_error->getMessage($params['name']);
    if ($message === null) {
        return '';
    }
    if (isset($params['add_msg']) && $params['add_msg']) {
        $message = $params['add_msg'] . $message;
    }

    $msg = sprintf('<div class="alert alert-sm alert-danger">%s</div>', $message);
    $offsetNum = 2;
    $offset = 'col-sm-offset-2 ';
    if (isset($params['offset']) && strlen($params['offset'])) {
        if ($params['offset']) {
            $offsetNum = (int)$params['offset'];
            $offset = 'col-sm-offset-' . $offsetNum . ' ';
        } else {
            $offset = '';
            $offsetNum = 0;
        }
    }
    $id = '';
    if (isset($params['id']) && strlen($params['id']) > 0) {
        $id = sprintf(' id="%s"', $params['id']);
    }
    return sprintf('<div class="row"%s><div class="%scol-sm-%d">%s</div></div>', $id, $offset, 12 - $offsetNum, $msg);
}
?>
