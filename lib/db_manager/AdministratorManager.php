<?php
/**
 *  lib/db_manager/AdministratorManager.php
 * 
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 */

/**
 *  administratorテーブルアクセスクラス
 * 
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class AdministratorManager extends DB
{
    /**
     * コンストラクタ
     *
     * @access public
     */
    function AdministratorManager()
    {
        // 接続
        parent::connect();
    }

    /**
     * 管理者ログイン情報取得
     *
     * @access public
     * @param  array   $search  検索条件配列
     * @return array  管理者情報
     */
    function login($search)
    {
        if (strlen($search['account']) === 0 || strlen($search['password']) === 0) {
            return array();
        }
        $where = '';
        $condition = array();
        // アカウント
        $condition[] = sprintf("account = '%s'", parent::escape($search['account']));
        // パスワード
        $condition[] = sprintf("password = '%s'", CommonUtil::hashPassword(parent::escape($search['password'])));
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }
        // SQL設定
        $sql = sprintf('SELECT id' .
                       '     , name' .
                       '     , account' .
                       '     , password' .
                       '     , master_flg' .
                       '  FROM administrator' .
                       '%s',
                       $where
                      );
        $result = parent::query($sql);
        return parent::fetchOne($result);
    }

    /**
     * 管理者情報取得
     *
     * @access public
     * @param  array  $search  検索条件配列
     * @return array  管理者情報
     */
    function get($search)
    {
        $where = '';
        $condition = array();
        // ID
        if (isset($search['id']) && strlen($search['id']) > 0) {
            $condition[] = sprintf('id = %d', (int)$search['id']);
        }
        // アカウント
        if (isset($search['account']) && strlen($search['account']) > 0) {
            $condition[] = sprintf("account = '%s'", parent::escape($search['account']));
        }
        // パスワード
        if (isset($search['password']) && strlen($search['password']) > 0) {
            $condition[] = sprintf("password = '%s'", parent::escape($search['password']));
        }
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }

        // SQL設定
        $sql = sprintf('SELECT id' .
                       '     , name' .
                       '     , account' .
                       '     , password' .
                       '     , master_flg' .
                       '  FROM administrator' .
                       '%s',
                       $where
                      );
        $result = parent::query($sql);
        return parent::fetchOne($result);
    }
    /**
     * 管理者一覧情報取得
     *
     * @access public
     * @param  array   $search     検索条件配列
     * @param  string  $foundRows  FOUND_ROWSオプション
     * @return array  管理者一覧情報
     */
    function getList($search = array(), $foundRows = '')
    {
        $where = '';
        $condition = array();
        // アカウント
        if (isset($search['account']) && strlen($search['account']) > 0) {
            $condition[] = sprintf("account = '%s'", parent::escape($search['account']));
        }
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }

        // SQL設定
        $sql = sprintf('SELECT %s' .
                       '       id' .
                       '     , name' .
                       '     , account' .
                       '     , password' .
                       '     , master_flg' .
                       '  FROM administrator' .
                       '%s',
                       $foundRows,
                       $where
                      );
        if (isset($search['offset']) && isset($search['limit'])) {
            $sql .= sprintf(' LIMIT %d, %d',
                            (int)$search['offset'],
                            (int)$search['limit']
                           );
        }
        $result = parent::query($sql);
        return parent::fetchAll($result);
    }

    /**
     * 管理者情報重複チェック
     *
     * @access public
     * @param  array  $search  検索条件配列
     * @return integer  重複件数
     */
    function isDuplication($search)
    {
        $where = '';
        $condition = array();
        // アカウント
        if (isset($search['account']) && strlen($search['account']) > 0) {
            $condition[] = sprintf("account = '%s'", parent::escape($search['account']));
        }
        // ID
        if (isset($search['id']) && strlen($search['id']) > 0) {
            $condition[] = sprintf('id <> %d', (int)$search['id']);
        }
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }

        // SQL設定
        $sql = 'SELECT COUNT(id) AS count' .
               '  FROM administrator' .
               $where;
        $result = parent::query($sql);
        return parent::result($result, 'count');
    }

    /**
     * 管理者情報登録
     *
     * @access public
     * @param  array  $value  登録する情報配列
     * @return integer  直近のID
     */
    function insert($value)
    {
        return parent::insert('administrator', $value);
    }

    /**
     * 管理者情報更新
     *
     * @access public
     * @param  array  $value   更新する情報配列
     * @param  array  $search  更新する条件配列
     * @return integer  影響を与えたレコード数
     */
    function update($value, $search)
    {
        $where = '';
        $condition = array();
        // ID
        if (isset($search['id']) && strlen($search['id']) > 0) {
            $condition[] = sprintf("id = %d", (int)$search['id']);
        }
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf('%s', implode(' AND ', $condition));
        }
        return parent::update('administrator', $value, $where);
    }

    /**
     * 管理者情報削除
     *
     * @access public
     * @param  array  $search  削除する条件配列
     * @return integer  影響を与えたレコード数
     */
    function delete($search)
    {
        $where = '';
        $condition = array();
        // ID
        if (isset($search['id']) && strlen($search['id']) > 0) {
            $condition[] = sprintf("id = %d", (int)$search['id']);
        }
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf('%s', implode(' AND ', $condition));
        }
        return parent::delete('administrator', $where);
    }
}
?>