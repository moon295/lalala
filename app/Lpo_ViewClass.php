<?php
// vim: foldmethod=marker
/**
 *  Lpo_ViewClass.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.viewclass.php 323 2006-08-22 15:52:26Z fujimoto $
 */

// {{{ Lpo_ViewClass
/**
 *  viewクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_ViewClass extends Ethna_ViewClass
{
    var $memcacheUtil;

    /**
     *  共通値を設定する
     *
     *  @access protected
     *  @param  object  Lpo_Renderer  レンダラオブジェクト
     */
    function _setDefault(&$renderer)
    {
    }

    /**
     *  遷移名に対応する画面を出力する
     *
     *  特殊な画面を表示する場合を除いて特にオーバーライドする必要は無い
     *  (preforward()のみオーバーライドすれば良い)
     *
     *  @access public
     */
    function forward()
    {
        // MEMO
        // ここより下でsetAppをする場合は、その後に必ず「$renderer =& $this->_getRenderer();」を
        // 実行すること。
        // Smarty展開後のタグを変数に格納する。
        // $renderer =& $this->_getRenderer();
        if (strpos($this->backend->ac->actionName, 'admin') === 0) {
        }

        if (strstr($this->forward_path, 'default.tpl')) {
            // default.tplの場合はtpl_fileが設定が必須
            // テンプレートファイル設定確認
            $templateFile = $this->af->getAppNE('tpl_file');
            if (empty($templateFile)) {
                $this->af->setAppNE('error_message', 'テンプレートが設定されていません。');
                $templateFile = 'error';
            }
            $this->forward_path = TEMPLATE_DIR . '/' . str_replace('_', '/', $templateFile) . '.tpl';
        }

        // Viewでテンプレート切り替え時に再度アクションなどを設定する
        $actionName        = str_replace('.tpl', '', str_replace('/', '_', $this->forward_path));
        $parentActionName  = preg_replace('/_[^_]+$/', '', $actionName);
        $originActionName  = preg_replace('/_[^_]+$/', '', $parentActionName);
        $this->af->setAppNE('action_name', $actionName);
        $this->af->setAppNE('parent_action_name', $parentActionName);
        $this->af->setAppNE('origin_action_name', $originActionName);

        $renderer =& $this->_getRenderer();

        $this->_setDefault($renderer);
        $tag = $renderer->perform($this->forward_path, true);
/*
        // JSコメントアウト除去
        $tag = preg_replace("/ +\/\/.+/i", "", $tag);
        $tag = preg_replace("/\/\*.+\*\//isU", "", $tag);
        // 不要タグ・文字の除去
        $tag = preg_replace("/  +|\n+/", '', $tag);
*/
//        $tag = preg_replace("/\n+/", "", $tag);
//        $tag = preg_replace("/[ ]*([\=\\-+\\*\\(\\){},:&\\|><\\?\\/]+)[ ]*/", "$1", $tag);

        //header('Content-Type: text/html charset="utf-8"');
        echo $tag;
    }
}
// }}}
?>
