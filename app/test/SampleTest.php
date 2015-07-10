<?php
/**
 * SampleTest.php
 * 
 * @author    {$author}
 * @package   Lpo.Test
 * @version   $Id$
 */

/**
 * Sample TestCase 
 * 
 * @author    {$author}
 * @package   Lpo.Test
 * @version   1.0
 */
class Sample_TestCase extends Ethna_UnitTestCase
{
    /**
     * テストの初期化
     * 
     * @access public
     */
    function setUp()
    {
        // TODO: テストに際しての初期化コードを記述してください
        // 例: テスト用のデータをDBから読み込む
    }
    
    /**
     * テストの後始末
     * 
     * @access public
     */
    function tearDown()
    {
        // TODO: テスト終了に際してのコードを記述してください
        // 例: テスト用のデータから開発用のデータに戻す
    }
    
    /**
     * サンプルのテストケース
     * 
     * @access public
     */
    function test_Sample()
    {
        /**
         *  TODO: テストケースを記述して下さい。
         *  @see http://simpletest.org/en/first_test_tutorial.html
         *  @see http://simpletest.org/en/unit_test_documentation.html
         */
        $this->fail('No Test! write Test!');
        $this->assertTrue(true);
    }
}

?>
