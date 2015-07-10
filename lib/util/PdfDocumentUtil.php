<?php
/**
 *  lib/util/PdfDocumentUtil.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
*/

/**
 *  PdfDocumentUtilクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class PdfDocumentUtil
{
    /**
     *  @access public
     *  @var  object  Ethnaオブジェクト
     */
    var $ethna;

    /**
     *  @access public
     *  @var  object  TCPDFオブジェクト
     */
    var $fpdf;

    /**
     *  @access public
     *  @var  string  書類種別
     */
    var $type;

    /**
     *  コンストラクタ
     *
     *  @access public
     *  @param  object  $ethna  Ethnaオブジェクト
     *  @return void
     */
    function PdfDocumentUtil(&$ethna)
    {
        require_once BASE . '/lib/pdf/tcpdf/tcpdf.php';
        require_once BASE . '/lib/pdf/fpdi/fpdi.php';

        $this->ethna = $ethna;

        $this->fpdf = new FPDI(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->fpdf->setPrintHeader(false);
    }

    /**
     *  PDF出力
     *
     *  @access public
     *  @param  object  $ethna  Ethnaオブジェクト
     *  @return void
     */
    function output($fname)
    {
//        $this->fpdf->Output($fname, 'I');
        $this->fpdf->Output($fname, 'D');
    }

    /**
     *  外部PDFのページ追加
     *
     *  @access public
     *  @param  string  $path  外部PDFファイルパス
     *  @return void
     */
    function addTemplate($path, $pageNo = 1)
    {
        // 新規ページ追加
        $this->fpdf->AddPage();
        // 外部読み込みPDF設定
        $this->fpdf->setSourceFile($path);
        // ページのインポート
        $tplIdx = $this->fpdf->importPage($pageNo);
        // ページをテンプレートとして使用設定
        $this->fpdf->useTemplate($tplIdx);
    }

    /**
     *  請求書作成
     *
     *  @access public
     *  @param  array  $billList  請求書情報配列
     *  @return void
     */
    function createBill(&$billList)
    {
        // 納品書詳細取得
        $search = array('bill_id' => $data['id']);

        $border = 0;
        foreach ($billList as $bill) {
            $this->addTemplate(TEMPLATE_DIR . '/pdf/bill.pdf', 1);

            // 宛先
            $fontSize = 12;
            if (mb_strlen($bill['company_name'], 'UTF-8') > 33) {
                $fontSize = 9.5;
            }
            $this->fpdf->SetFont('kozgopromedium', '', $fontSize);
            $this->fpdf->SetXY(18, 50);
            $this->fpdf->Cell(149, 5, $bill['company_name'] . '  様', $border, 0, 'L');

            // 発行年月日
            $this->fpdf->SetFont('kozgopromedium', '', 8);
            $this->fpdf->SetXY(247, 48.5);
            $this->fpdf->Cell(28, 5, $bill['issue_date'], $border, 0, 'L');

            // ご利用月
            $this->fpdf->SetFont('kozgopromedium', '', 8);
            $this->fpdf->SetXY(247, 53.5);
            $this->fpdf->Cell(28, 5, $bill['use_date'], $border, 0, 'L');

            // 基本料金合計
            $this->fpdf->SetFont('kozgopromedium', '', 9.5);
            $this->fpdf->SetXY(169, 90.5);
            $this->fpdf->Cell(27, 5, $bill['basic_charge'], $border, 0, 'R');


            // 通話料金合計
            $this->fpdf->SetXY(196.5, 90.5);
            $this->fpdf->Cell(26.5, 5, $bill['call_charges'], $border, 0, 'R');

            // 料金合計
            $this->fpdf->SetXY(223, 90.5);
            $this->fpdf->Cell(53, 5, $bill['total_amount'], $border, 0, 'R');

            // 明細
            $y = 113.75;
            foreach ($bill['details'] as $detail) {
                // No
                $this->fpdf->SetFont('kozgopromedium', '', 7.5);
                $this->fpdf->SetXY(17.5, $y);
                $this->fpdf->Cell(11, 5, 'No.' . $detail['no'], $border, 0, 'L');

                // 発番番号
                $this->fpdf->SetFont('kozgopromedium', '', 9.5);
                $this->fpdf->SetXY(28, $y);
                $this->fpdf->Cell(72, 5, $detail['tel'], $border, 0, 'R');

                // 基本料金
                $this->fpdf->SetFont('kozgopromedium', '', 8);
                $this->fpdf->SetXY(100, $y);
                $this->fpdf->Cell(27, 5, $detail['basic_charge'], $border, 0, 'R');

                // 通差料金
                $this->fpdf->SetXY(127, $y);
                $this->fpdf->Cell(41, 5, $detail['call_charges'], $border, 0, 'R');

                // 備考
                $this->fpdf->SetXY(168, $y);
                $this->fpdf->Cell(108.5, 5, $detail['memo'], $border, 0, 'L');

                $y += 5.1;
            }
        }
    }
}
?>
