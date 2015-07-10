<?php
/**
 *  Lpo_Session.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  セッションクラス
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Session extends Ethna_Session
{
    function Lpo_Session($appid, $save_dir, $logger)
    {
        parent::Ethna_Session($appid, '', $logger);
    }

    /**
     *  セッション値へのアクセサ(R)（オーバーライド）
     *
     *  @access public
     *  @param  string  $name  キー
     *  @return mixed   取得した値(null:セッションが開始されていない)
     */
    function get($name)
    {
        if (strstr($name, '.') && strlen($name) > 1) {
            $names = explode('.', $name);
            $session = parent::get($names[0]);
            $pSession = &$session;
            $max = count($names);
            for ($i = 1; $i < $max; $i++) {
                $n = $names[$i];
                if (isset($pSession[$n])) {
                    $pSession = &$pSession[$n];
                } else {
                    return null;
                }
            }
            $session = $pSession;
        } else {
            $session = parent::get($name);
        }
        return $session;
    }

    /**
     *  セッション値へのアクセサ(W)（オーバーライド）
     *
     *  @access public
     *  @param  string  $name   キー
     *  @param  string  $value  値
     *  @return bool    true:正常終了 false:エラー(セッションが開始されていない)
     */
    function set($name, $value)
    {
        if (strstr($name, '.') && strlen($name) > 1) {
            $names = explode('.', $name);
            $session = parent::get($names[0]);
            $pSession = &$session;
            $max = count($names);
            for ($i = 1; $i < $max; $i++) {
                $n = $names[$i];
                if ($i < $max - 1) {
                    if (!isset($pSession[$n]) || !is_array($pSession[$n])) {
                        $pSession[$n] = array();
                    }
                    $pSession = &$pSession[$n];
                } else {
                    $pSession[$n] = $value;
                }
            }
            $name = $names[0];
            $value = $session;
        }
        return parent::set($name, $value);

    }

    /**
     *  セッションの値を破棄する
     *
     *  @access public
     *  @param  string  $name   キー
     *  @return bool    true:正常終了 false:エラー(セッションが開始されていない)
     */
    function remove($name)
    {
        if (strstr($name, '.') && strlen($name) > 1) {
            $names = explode('.', $name);
            $session = parent::get($names[0]);
            $pSession = &$session;
            $max = count($names);
            for ($i = 1; $i < $max; $i++) {
                $n = $names[$i];
                if (isset($pSession[$n])) {
                    if ($i < $max - 1) {
                        $pSession = &$pSession[$n];
                    } else {
                        unset($pSession[$n]);
                    }
                } else {
                    return false;
                }
            }
            $name = $names[0];
            $value = $session;
            return parent::set($name, $value);
        } else {
            return parent::remove($name, $value);
        }
    }
}
?>
