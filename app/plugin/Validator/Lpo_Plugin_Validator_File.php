<?php
// vim: foldmethod=marker
/**
 *  Lpo_Plugin_Validator_File.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: Lpo_Plugin_Validator_File.php 460 2007-04-11 13:12:30Z cocoitiban $
 */

// UPLOAD_ERR_* が未定義の場合
if (defined('UPLOAD_ERR_OK') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_OK', 0);
}
if (defined('UPLOAD_ERR_INI_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_INI_SIZE', 1);
}
if (defined('UPLOAD_ERR_FORM_SIZE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_FORM_SIZE', 2);
}
if (defined('UPLOAD_ERR_PARTIAL') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_PARTIAL', 3);
}
if (defined('UPLOAD_ERR_NO_FILE') == false) { // PHP 4.3.0
    define('UPLOAD_ERR_NO_FILE', 4);
}
if (defined('UPLOAD_ERR_NO_TMP_DIR') == false) { // PHP 4.3.10, 5.0.3
    define('UPLOAD_ERR_NO_TMP_DIR', 6);
}
if (defined('UPLOAD_ERR_CANT_WRITE') == false) { // PHP 5.1.0
    define('UPLOAD_ERR_CANT_WRITE', 7);
}

// {{{ Lpo_Plugin_Validator_File
/**
 *  ファイルチェックプラグイン
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Plugin_Validator_File extends Lpo_Plugin_Validator
{
    /** @var    bool    配列を受け取るかフラグ */
    var $accept_array = false;

    /**
     *  アップロードされたファイルのチェックを行う
     *  XXX: プラグインのエラーコードを修正する
     *
     *  @access public
     *  @param  string  $name       フォームの名前
     *  @param  mixed   $var        フォームの値
     *  @param  array   $params     プラグインのパラメータ
     */
    function &validate($name, $var, $params)
    {
        $true = true;
        if ($this->getFormType($name) != VAR_TYPE_FILE) {
            return $true;
        }

        // そもそもアップロードされていない場合はスキップ
        if ($var['error'] == UPLOAD_ERR_NO_FILE) {
            return $true;
        }


        // エラーコードの検査
        $msg = '';
        switch ($var['error']) {
        case UPLOAD_ERR_INI_SIZE: 
            $msg = 'アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。';
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $msg = 'アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。';
            break;
        case UPLOAD_ERR_PARTIAL:
            $msg= 'アップロードされたファイルは一部のみしかアップロードされていません。';
            break;
        case UPLOAD_ERR_NO_FILE:
            $msg = 'ファイルはアップロードされませんでした。';
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $msg = 'テンポラリフォルダがありません。';
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $msg= 'ディスクへの書き込みに失敗しました。';
            break;
        }
        if ($msg != '') {
            if (isset($params['error'])) {
                $msg = $params['error'];
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // tmp_name の検査
        if (isset($var['tmp_name']) == false || is_uploaded_file($var['tmp_name']) == false) {
            if (isset($params['error'])) {
                $msg = $params['error'];
            } else {
                $msg = 'tmp_name が不正です。';
            }
            return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
        }


        // size の検査
        if (isset($params['size_max'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] > $this->_getSizeAsBytes($params['size_max'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = 'ファイルサイズは%s以下にしてください。';
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_max']));
            }
        }
        if (isset($params['size_min'])) {
            $st = stat($var['tmp_name']);
            if ($st[7] < $this->_getSizeAsBytes($params['size_min'])) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = 'ファイルサイズは%s以上にしてください。';
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE, array($params['size_min']));
            }
        }


        // type の検査
        if (isset($params['type'])) {
            $type_list = to_array($params['type']);
            $posted_mime = explode('/', $var['type'], 2);
            foreach ($type_list as $type) {
                $wanted_mime = explode('/', $type, 2);
                $test = (count($wanted_mime) == 1)
                        ? (strcasecmp($wanted_mime[0], $posted_mime[0]) == 0)
                : (strcasecmp($type, $var['type']) == 0);  
                if ($test == true) {
                    break;
                }
            }
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = 'ファイルタイプが正しくありません。';
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        // name(ファイル名)の検査
        if (isset($params['name'])) {
            $test = ($params['name']{0} == '/')
                ? preg_match($params['name'], $var['name'])
                : (strcmp($params['name'], $var['name']) == 0);
            if ($test == false) {
                if (isset($params['error'])) {
                    $msg = $params['error'];
                } else {
                    $msg = 'ファイル名が正しくありません。';
                }
                return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
            }
        }

        // image(画像タイプ)の検査
        if (isset($params['img_type'])) {
            $type_list = to_array($params['img_type']);
            if (is_file($var['tmp_name']) && filesize($var['tmp_name']) > 0) {
                $type = GetImageSize($var['tmp_name']);
                if (isset($type_list[$type[2]]) != true) {
                    if (isset($params['error'])) {
                        $msg = $params['error'];
                    } else {
                        $msg = sprintf('画像タイプが正しくありません。[%s]', implode(',', $type_list));
                    }
                    return Ethna::raiseNotice($msg, E_FORM_WRONGTYPE_FILE);
                }
            }
        }

        return $true;
    }


    function _getSizeAsBytes($size)
    {
        $unit = 1;
        if (preg_match('/^([0-9\.]+)([mk])?(b(ytes?)?)?$/i', trim($size), $matches)) {
            if (isset($matches[1])) {
                $size = $matches[1];
            }
            if (isset($matches[2])) {
                if (strtolower($matches[2]) === 'm') {
                    $unit = 1048576;
                } else if (strtolower($matches[2]) === 'k') {
                    $unit = 1024;
                }
            }
        }
        return intval($matches[1]) * $unit;
    }
}
// }}}
?>
