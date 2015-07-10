<?php
/**
 *  lib/db_manager/ShopManager.php
 * 
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 */

/**
 *  shopテーブルアクセスクラス
 * 
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class ShopManager extends DB
{
    /**
     * コンストラクタ
     *
     * @access public
     */
    function ShopManager()
    {
        // 接続
        parent::connect();
    }

    /**
     * 店舗一覧情報取得
     *
     * @access public
     * @param  array   $search     検索条件配列
     * @param  string  $foundRows  FOUND_ROWSオプション
     * @return array  顧客一覧情報
     */
    function getList($search = array(), $foundRows = '')
    {
        $where = '';
        $condition = array();
        // パラメータのエスケープ処理
        $search = parent::escapeArray($search);
        // 会社名
        if (isset($search['company_name']) && strlen($search['company_name']) > 0) {
            $condition[] = sprintf("name LIKE '%%%s%%'", $search['company_name']);
        }
        // 削除フラグ
        $condition[] = 'id_deleted = 0';
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }

        // SQL設定
        $sql = sprintf('SELECT %s' .
                       '       id' .
                       '     , name' .
                       '     , created' .
                       '     , updated' .
                       '  FROM shops' .
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
     * ショップ情報取得
     *
     * @access public
     * @param  array  $search  検索条件配列
     * @return array  ショップ情報
     */
    function get($search)
    {
        $where = '';
        $condition = array();
        // パラメータのエスケープ処理
        $search = parent::escapeArray($search);
        // YELP ID
        if (isset($search['yelp_id']) && strlen($search['yelp_id']) > 0) {
            $condition[] = sprintf("yelp_id = '%s'", $search['yelp_id']);
        }
        // 削除フラグ
        $condition[] = 'id_deleted = 0';
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }
        // SQL設定
        $sql = sprintf('SELECT id' .
                       '     , yelp_id' .
                       '     , name' .
                       '  FROM shops' .
                       '%s',
                       $where
                      );
        $result = parent::query($sql);
        return parent::fetchOne($result);
    }

    /**
     * 顧客情報重複チェック
     *
     * @access public
     * @param  array  $search  検索条件配列
     * @return integer  重複件数
     */
    function isDuplication($search)
    {
        $where = '';
        $condition = array();
        // パラメータのエスケープ処理
        $search = parent::escapeArray($search);
        // メールアドレス
        if (isset($search['mail']) && strlen($search['mail']) > 0) {
            $condition[] = sprintf("mail = '%s'", $search['mail']);
        }
        // ID
        if (isset($search['id']) && strlen($search['id']) > 0) {
            $condition[] = sprintf('id <> %d', (int)$search['id']);
        }
        // 削除フラグ
        $condition[] = 'id_deleted = 0';
        // WHERE句の結合
        if (count($condition) > 0) {
            $where = sprintf(' WHERE %s', implode(' AND ', $condition));
        }
        // SQL設定
        $sql = 'SELECT COUNT(id) AS count' .
               '  FROM shop' .
               $where;
        $result = parent::query($sql);
        return parent::result($result, 'count');
    }

    /**
     * 顧客情報登録
     *
     * @access public
     * @param  array  $value  登録する情報配列
     * @return integer  直近のID
     */
    function insert($value)
    {
        return parent::insert('shops', $value);
    }

    /**
     * 顧客情報更新
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
        return parent::update('shop', $value, $where);
    }

    /**
     * 顧客情報削除
     *
     * @access public
     * @param  array  $search  削除する条件配列
     * @return integer  影響を与えたレコード数
     */
    function delete($search)
    {
        $value = array('id_deleted'   => 1,
                       '#update_time' => 'NOW()',
                      );
        return $this->update($value, $search);
    }
}
?>
