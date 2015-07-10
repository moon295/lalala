<?php
/**
 *  lib/DB.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.view.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/etc/db_config.php';
/**
 *  データベースクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class DB
{
    /**
     *  @access protected
     *  @var  resource  データベース接続リソース
     */
    static $connect;

    /**
     *  @access protected
     *  @var  string  エラーメッセージ
     */
    static $errmsg;

    /**
     *  データベース接続処理
     *
     *  @access public
     *  @param  string  $code  データベースサーバー情報コード
     *  @return resource  データベース接続リソース
     */
    public function connect($code = 'slave')
    {
        if (!isset(self::$connect[$code]) || !is_resource(self::$connect[$code])) {
            // コネクションが存在しない場合
            if (!is_array(self::$connect)) {
                self::$connect = array();
            }
            try {
                // DBサーバーリスト取得
                $dbList = unserialize(DB_LIST);
                // マスターDB設定確認
                if (!isset($dbList['master'])) {
                    throw new Exception('マスターDB設定が行われていません。');
                }
                // マスターDBのみか確認
                $masterOnlyFlg = !isset($dbList['slave']) || serialize($dbList['master']) === serialize($dbList['slave']);
                // スレーブ設定がない場合もしくはマスターとスレーブが同じ設定の場合
                if ($masterOnlyFlg) {
                    $code = 'master';
                }
                if (!isset(self::$connect[$code]) || !is_resource(self::$connect[$code])) {
                    $dbInfo = $dbList[$code];
                    if (strlen($dbList[$code]['pass']) === 0) {
                        self::$connect[$code] = @mysql_connect($dbInfo['server'], $dbInfo['user']);
                    } else {
                        self::$connect[$code] = @mysql_connect($dbInfo['server'], $dbInfo['user'], $dbInfo['pass']);
                    }
                    if (!is_resource(self::$connect[$code])) {
                        throw new Exception('データベースへの接続が確立できません。');
                    } else {
                        // マスターDBのみの場合、スレーブにもマスター接続リソースを設定
                        if ($masterOnlyFlg) {
                            self::$connect['slave'] = self::$connect['master'];
                        }
                        if (!mysql_select_db($dbInfo['dbname'], self::$connect[$code])) {
                            throw new Exception('データベースが存在しません。');
                        }
                    }
                    mysql_query("SET NAMES utf8;", self::$connect[$code]);
                } else if (!$masterOnlyFlg && !mysql_select_db($dbList[$code]['dbname'], self::$connect[$code])) {
                    throw new Exception('データベースが存在しません。');
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                exit();
            }
        }
        return self::$connect[$code];
    }

    /**
     *  エラーメッセージを取得
     *
     *  @access public
     *  @return string  エラーメッセージ
     */
    public function getError()
    {
        return self::$errmsg;
    }

    /**
     *  MySQLエスケープ
     *
     *  @access public
     *  @param  string   $str         エスケープする文字列
     *  @param  boolean  $noWildCard  true:ワイルドカードエスケープ / false:ワイルドカード非エスケープ
     *  @return string  変換された文字列
     */
    public function escape($str, $noWildCard = true)
    {
        if (strlen($str) > 0) {
            if ($noWildCard) {
                $str = str_replace(array('\\', '_', '%'), array('\\\\', '\_', '\%'), $str);
            }
            return mysql_real_escape_string($str);
        }
        return $str;
    }

    /**
     *  MySQLエスケープ配列用
     *
     *  @access public
     *  @param  string   $data        エスケープする配列
     *  @param  boolean  $noWildCard  true:ワイルドカードエスケープ / false:ワイルドカード非エスケープ
     *  @return string  変換された文字列
     */
    public function escapeArray($data, $noWildCard = true)
    {
        if (is_array($data)) {
            foreach ($data as $key => $str) {
                if (is_array($str)) {
                    $data[$key] = self::escapeArray($str, $noWildCard);
                } else if (is_numeric($str)) {
                    $data[$key] = $str;
                } else {
                    $data[$key] = self::escape($str, $noWildCard);
                }
            }
        }
        return $data;
    }

    /**
     *  全件取得処理
     *
     *  @access public
     *  @param  resource  $result  クエリ結果
     *  @return array  データ配列
     */
    public function fetchAll(&$result)
    {
        $data = array();
        if (is_resource($result)) {
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     *  1件取得処理
     *
     *  @access public
     *  @param  resource  $result  クエリ結果
     *  @return mixed  データ配列（データがない場合FALSE）
     */
    public function fetchOne(&$result)
    {
        if (is_resource($result)) {
            return mysql_fetch_assoc($result);
        }
        return false;
    }

    /**
     *  1カラム取得処理
     *
     *  @access public
     *  @param  integer  $code  データベースサーバー情報コード
     *  @return string  カラム情報
     */
    public function result(&$result, $column)
    {
        if (is_resource($result) && mysql_num_rows($result)) {
            return mysql_result($result, 0, $column);
        }
        return '';
    }

    /**
     *  SQLクエリを実行する
     *
     *  @access public
     *  @param  string   $sql   SQLクエリ
     *  @param  boolean  $mode  false:直接コール / true:間接コール
     *  @param  string   $code  データベースサーバー情報コード
     *  @return resource  クエリリソース
     */
    public function query($sql, $code = 'slave', $mode = false)
    {
        if (isset($_SESSION['sql_debug'])) {
            print $sql;
            unset($_SESSION['sql_debug']);
        }

        if (!isset(self::$connect[$code])) {
            die('DB接続が見つかりません');
        }

        require BASE . '/etc/lpo-ini.php';
        if (isset($config['sql']['file'])) {
            $trace = debug_backtrace();
            $logSql = preg_replace('/ +/', ' ', $sql);
            $logFile = $config['sql']['file'];
            $newFlg = false;
            if (!is_file($logFile)) {
                $newFlg = true;
            }
            // 実行URL
            $fp = fopen($logFile, 'a');
            if (empty($config['sql']['no_log_word']) || !preg_match("/{$config['sql']['no_log_word']}/", $logSql)) {
                $requestUri = '';
                if (isset($_SERVER) && is_array($_SERVER)) {
                    if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) {
                        $requestUri = sprintf('[%s(%s)]', $_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);
                    }
                }
                if (!$mode) {
                    fputs($fp, sprintf("%s [SQL][%s(%d)]%s: %s\n", date('Y/m/d H:i:s'), str_replace(BASE, '', $trace[0]['file']), $trace[0]['line'], $requestUri, $logSql));
                } else {
                    fputs($fp, sprintf("%s [SQL][%s(%d)]%s: %s\n", date('Y/m/d H:i:s'), str_replace(BASE, '', $trace[1]['file']), $trace[1]['line'], $requestUri, $logSql));
                }
            }
            $result = mysql_query($sql, self::$connect[$code]);
            if (!is_resource($result)) {
                $err = mysql_error(self::$connect[$code]);
                if (!empty($err)) {
                    $traceInfo = $trace[0];
                    if (!$mode) {
                        $traceInfo = $trace[1];
                    }
                    $errLog = sprintf("%s [ERROR]\n" .
                                      "    実行場所：%s(%d)\n".
                                      "    呼出URL ：%s\n" .
                                      "    実行SQL ：%s\n" .
                                      "    エラー  ：%s%s\n",
                                      date('Y/m/d H:i:s'),
                                      str_replace(BASE, '', $traceInfo['file']), $traceInfo['line'],
                                      $requestUri,
                                      $logSql,
                                      $err,
                                      self::getFormatParameters()
                                     );
                    if (DEBUG) {
                        pr($errLog);
                    }
                    fputs($fp, $errLog);
                }
            }
            fclose($fp);
            if ($newFlg) {
                if (filesize($logFile) === 0) {
                    unlink($logFile);
                } else {
                    chmod($logFile, 0666);
                }
            }
        } else {
            $result = mysql_query($sql, self::$connect[$code]);
        }
        return $result;
    }

    /**
     *  INSERTクエリを実行する
     *
     *  @access public
     *  @param  string   $table   テーブル名
     *  @param  array    $values  挿入データ配列
     *  @param  boolean  $select  false:通常のINSERT / true:VALUESにSELECT
     *  @return integer  INSERTで生成されたIDを得る
     */
    public function insert($table, $values, $select = false)
    {
        $code = 'master';
        self::connect($code);

        $fieldList = array();
        $valueList = array();
        foreach ($values as $key => $val) {
            if (preg_match('/^#/', $key)) {
                $fieldList[] = substr($key, 1);
                if (is_numeric($val)) {
                    if (strstr($val, '.')) {
                        $valueList[] = (float)$val;
                    } else {
                        $valueList[] = (int)$val;
                    }
                } else {
                    $valueList[] = $val;
                }
            } else {
                $fieldList[] = $key;
                $valueList[] = sprintf("'%s'", self::escape($val, false));
            }
        }
        if ($select) {
            $sql = sprintf("INSERT INTO %s (`%s`) (SELECT %s)",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        } else {
            $sql = sprintf("INSERT INTO %s (`%s`) VALUES(%s)",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        }
        $result = DB::query($sql, $code, true);
        $id = 0;
        if ($result) {
            $id = mysql_insert_id(self::$connect[$code]);
            if ($id === 0) {
                // ID生成されなかった場合
                $id = $result;
            }
        }
        return $id;
    }

    /**
     *  UPDATEクエリを実行する
     *
     *  @access public
     *  @param  string   $table   テーブル名
     *  @param  array    $values  更新データ配列
     *  @param  string   $where   WHERE句
     *  @return integer  クエリで変更された行数
     */
    public function update($table, $values, $where)
    {
        $code = 'master';
        self::connect($code);

        $valueList = array();
        foreach ($values as $key => $val) {
            if (preg_match('/^#/', $key)) {
                if (is_numeric($val)) {
                    if (strstr($val, '.')) {
                        $valueList[] = sprintf("`%s` = %s", substr($key, 1), (float)$val);
                    } else {
                        $valueList[] = sprintf("`%s` = %s", substr($key, 1), (int)$val);
                    }
                } else {
                    $valueList[] = sprintf("`%s` = %s", substr($key, 1), $val);
                }
            } else {
                $valueList[] = sprintf("`%s` = '%s'", $key, self::escape($val, false));
            }
        }
        $sql = sprintf("UPDATE %s SET %s",
                       $table,
                       implode(',', $valueList)
                      );
        if (!empty($where)) {
            $sql .= sprintf(' WHERE %s', $where);
        }
        DB::query($sql, $code, true);
        return mysql_affected_rows(self::$connect[$code]);
    }

    /**
     *  DELETEクエリを実行する
     *
     *  @access public
     *  @param  string   $table   テーブル名
     *  @param  string   $where   WHERE句
     *  @return integer  クエリで削除された行数
     */
    public function delete($table, $where)
    {
        $code = 'master';
        self::connect($code);

        $sql = sprintf("DELETE FROM %s",
                       $table
                      );
        if (!empty($where)) {
            $sql .= sprintf(' WHERE %s', $where);
        }
        DB::query($sql, $code, true);
        return mysql_affected_rows(self::$connect[$code]);
    }

    /**
     *  TRUNCATEクエリを実行する
     *
     *  @access public
     *  @param  string   $table   テーブル名
     *  @return integer  クエリで削除された行数
     */
    public function truncate($table)
    {
        $code = 'master';
        self::connect($code);

        $sql = sprintf("TRUNCATE TABLE %s",
                       $table
                      );
        if (!empty($where)) {
            $sql .= sprintf(' WHERE %s', $where);
        }
        return DB::query($sql, $code, true);
    }

    /**
     *  REPLACEクエリを実行する
     *
     *  @access public
     *  @param  string   $table   テーブル名
     *  @param  array    $values  挿入データ配列
     *  @param  boolean  $nextId  true:直近のID / false:クエリで影響を与えた行数
     *  @param  boolean  $select  false:通常のINSERT / true:VALUESにSELECT
     *  @return integer  REPLACEで生成されたIDを得る
     */
    public function replace($table, $values, $nextId = true, $select = false)
    {
        $code = 'master';
        self::connect($code);

        $fieldList = array();
        $valueList = array();
        foreach ($values as $key => $val) {
            if (preg_match('/^#/', $key)) {
                $fieldList[] = substr($key, 1);
                if (is_numeric($val)) {
                    if (strstr($val, '.')) {
                        $valueList[] = (float)$val;
                    } else {
                        $valueList[] = (int)$val;
                    }
                } else {
                    $valueList[] = $val;
                }
            } else {
                $fieldList[] = $key;
                $valueList[] = sprintf("'%s'", self::escape($val, false));
            }
        }
        if ($select) {
            $sql = sprintf("REPLACE INTO %s (`%s`) (SELECT %s)",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        } else {
            $sql = sprintf("REPLACE INTO %s (`%s`) VALUES(%s)",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        }
        DB::query($sql, $code, true);
        if ($nextId) {
            return mysql_insert_id(self::$connect[$code]);
        } else {
            return mysql_affected_rows(self::$connect[$code]);
        }
    }

    /**
     *  バルクINSERTクエリを実行する
     *
     *  @access public
     *  @param  string   $table    テーブル名
     *  @param  array    $values   挿入データ配列
     *  @param  boolean  $replace  false:INSERT / true:REPLACE
     *  @return integer  クエリで挿入された行数
     */
    public function bulkInsert($table, $values, $replace = false)
    {
        $code = 'master';
        self::connect($code);

        $fieldFlg  = false;
        $fieldList = array();
        $valueList = array();
        foreach ($values as $vals) {
            $list = array();
            foreach ($vals as $key => $val) {
                if (preg_match('/^#/', $key)) {
                    if (!$fieldFlg) {
                        $fieldList[] = substr($key, 1);
                    }
                    if (is_numeric($val)) {
                        if (strstr($val, '.')) {
                            $list[] = (float)$val;
                        } else {
                            $list[] = (int)$val;
                        }
                    } else {
                        $list[] = $val;
                    }
                } else {
                    if (!$fieldFlg) {
                        $fieldList[] = $key;
                    }
                    $list[] = sprintf("'%s'", self::escape($val, false));
                }
            }
            $valueList[] = sprintf('(%s)', implode(',', $list));
            $fieldFlg = true;
        }
        if ($replace) {
            $sql = sprintf("REPLACE INTO %s (`%s`) VALUES %s",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        } else {
            $sql = sprintf("INSERT INTO %s (`%s`) VALUES %s",
                           $table,
                           implode('`,`', $fieldList),
                           implode(',', $valueList)
                          );
        }
        DB::query($sql, $code, true);
        return mysql_affected_rows(self::$connect[$code]);
    }

    /**
     *  SELECT時のLIMITを無視した件数を取得
     *  SELECT時に「SQL_CALC_FOUND_ROWS」を付加した場合
     *
     *  @access public
     *  @param  string  $code  データベースサーバー情報コード
     *  @return integer
     */
    public function getRows($code = 'slave')
    {
        $result = DB::query('SELECT FOUND_ROWS() AS rows', $code, true);
        return mysql_result($result, 0, 'rows');
    }

    /**
     *  トランザクション開始
     *
     *  @access public
     *  @return void
     */
    public function begin()
    {
        $code = 'master';
        self::connect($code);
        DB::query('BEGIN', $code, true);
    }

    /**
     *  トランザクション確定
     *
     *  @access public
     *  @return void
     */
    public function commit()
    {
        $code = 'master';
        self::connect($code);
        DB::query('COMMIT', $code, true);
    }

    /**
     *  トランザクション戻す
     *
     *  @access public
     *  @return void
     */
    public function rollback()
    {
        $code = 'master';
        self::connect($code);
        DB::query('ROLLBACK', $code, true);
    }

    /**
     *  DBに登録、更新する際の日付データの整形
     *
     *  @access public
     *  @param  string  $name     対象配列要素名
     *  @param  array   $values   登録、更新元情報
     *  @param  array   $dbValue  登録、更新値配列（参照）
     *  @param  string  $dbName   第3引数の連想配列名
     *  @return void
     */
    public function formatDate($name, $values, &$dbValue, $dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $name;
        }
        if (isset($values[$name])) {
            if (!empty($values[$name]['Date_Year']) && !empty($values[$name]['Date_Month']) && !empty($values[$name]['Date_Day'])) {
                $dbValue[$dbName] = sprintf('%04d-%02d-%02d', $values[$name]['Date_Year'], $values[$name]['Date_Month'], $values[$name]['Date_Day']);
            } else {
                $dbValue['#' . $dbName] = 'NULL';
            }
        } else {
            $dbValue['#' . $dbName] = 'NULL';
        }
    }

    /**
     *  DBに登録、更新する際の時間データの整形
     *
     *  @access public
     *  @param  string  $name     対象配列要素名
     *  @param  array   $values   登録、更新元情報
     *  @param  array   $dbValue  登録、更新値配列（参照）
     *  @param  string  $dbName   第3引数の連想配列名
     *  @return void
     */
    public function formatTime($name, $values, &$dbValue, $dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $name;
        }
        if (isset($values[$name])) {
            if (!empty($values[$name]['Time_Hour'])) {
                $dbValue[$dbName] = sprintf('%02d:%02d', $values[$name]['Time_Hour'], $values[$name]['Time_Min']);
            } else {
                $dbValue['#' . $dbName] = 'NULL';
            }
        } else {
            $dbValue['#' . $dbName] = 'NULL';
        }
    }

    /**
     *  DBに登録、更新する際のNULL保存可能文字列
     *
     *  @access public
     *  @param  string  $name     対象配列要素名
     *  @param  array   $values   登録、更新元情報
     *  @param  array   $dbValue  登録、更新値配列（参照）
     *  @param  string  $dbName   第3引数の連想配列名
     *  @return void
     */
    public function formatText($name, $values, &$dbValue, $dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $name;
        }
        if (isset($values[$name])) {
            if (!is_null($values[$name]) && strlen($values[$name]) > 0) {
                $dbValue[$dbName] = $values[$name];
            } else {
                $dbValue['#' . $dbName] = 'NULL';
            }
        } else {
            $dbValue['#' . $dbName] = 'NULL';
        }
    }

    /**
     *  DBに登録、更新する際のNULL保存可能数値
     *
     *  @access public
     *  @param  string  $name     対象配列要素名
     *  @param  array   $values   登録、更新元情報
     *  @param  array   $dbValue  登録、更新値配列（参照）
     *  @param  string  $dbName   第3引数の連想配列名
     *  @return void
     */
    public function formatInt($name, $values, &$dbValue, $dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $name;
        }
        if (isset($values[$name])) {
            if (!is_null($values[$name]) && strlen($values[$name]) > 0) {
                $dbValue['#' . $dbName] = $values[$name];
            } else {
                $dbValue['#' . $dbName] = 'NULL';
            }
        } else {
            $dbValue['#' . $dbName] = 'NULL';
        }
    }

    /**
     *  DBに登録、更新する際のNULL保存可能小数点
     *
     *  @access public
     *  @param  string  $name     対象配列要素名
     *  @param  array   $values   登録、更新元情報
     *  @param  array   $dbValue  登録、更新値配列（参照）
     *  @param  string  $dbName   第3引数の連想配列名
     *  @return void
     */
    public function formatFloat($name, $values, &$dbValue, $dbName = null)
    {
        if (is_null($dbName)) {
            $dbName = $name;
        }
        if (isset($values[$name])) {
            if (!is_null($values[$name]) && strlen($values[$name]) > 0) {
                $dbValue['#' . $dbName] = $values[$name];
            } else {
                $dbValue['#' . $dbName] = 'NULL';
            }
        } else {
            $dbValue['#' . $dbName] = 'NULL';
        }
    }

    /**
     *  整形された環境変数等のパラメータ取得
     *
     *  @access public
     *  @return string  整形された環境変数等のパラメータ文字列
     */
    function getFormatParameters($indent = '    ')
    {
        $val = '';
        if (isset($_GET) && is_array($_GET) && count($_GET) > 0) {
            $val .= "\n{$indent}\$_GET" . preg_replace("/^|\n/", "\n{$indent}    ",  trim(print_r($_GET, true)));
        }
        if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
            $val .= "\n{$indent}\$_POST" . preg_replace("/^|\n/", "\n{$indent}    ",  trim(print_r($_POST, true)));
        }
        if (isset($_SERVER) && is_array($_SERVER) && count($_SERVER) > 0) {
            unset($_SERVER['HTTP_ACCEPT']);
            unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            unset($_SERVER['HTTP_ACCEPT_CHARSET']);
            unset($_SERVER['HTTP_ACCEPT_ENCODING']);
            unset($_SERVER['HTTP_CONNECTION']);
            unset($_SERVER['HTTP_TE']);
            unset($_SERVER['PATH']);
            unset($_SERVER['SystemRoot']);
            unset($_SERVER['COMSPEC']);
            unset($_SERVER['PATHEXT']);
            unset($_SERVER['WINDIR']);
            unset($_SERVER['SERVER_SIGNATURE']);
            unset($_SERVER['SERVER_ADMIN']);
            unset($_SERVER['GATEWAY_INTERFACE']);
            unset($_SERVER['SERVER_PROTOCOL']);
            $val .= "\n{$indent}\$_SERVER" . preg_replace("/^|\n/", "\n{$indent}    ",  trim(print_r($_SERVER, true)));
        }
        if (isset($_SESSION) && is_array($_SESSION) && count($_SESSION) > 0) {
            $val .= "\n{$indent}\$_SESSION" . preg_replace("/^|\n/", "\n{$indent}    ",  trim(print_r($_SESSION, true)));
        }
        return $val;
    }
}
?>