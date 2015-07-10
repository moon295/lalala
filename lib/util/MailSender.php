<?php
/**
 *  lib/MailSender.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 */

/**
 *  メール送信クラスのラッパークラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 */
class MailSender extends Ethna_MailSender
{
    // {{{ encode_MIME
    /**
     *  文字列をMIMEエンコードする
     *
     *  @access public
     *  @param  string  $string     MIMEエンコードする文字列
     *  @return エンコード済みの文字列
     */
    function encode_MIME($string)
    {
        mb_internal_encoding("UTF-8");
        $pos = 0;
        $split = 36;
        $_string = "";
        while ($pos < mb_strlen($string))
        {
            $tmp = mb_strimwidth($string, $pos, $split, "");
            $pos += mb_strlen($tmp);
            $_string .= (($_string)? ' ' : '') . mb_encode_mimeheader($tmp);
        }
        return $_string;
    }

    /**
     *  テンプレートメールのヘッダ情報を取得する（オーバーライド）
     *
     *  @access private
     *  @param  string  $mail   メールテンプレート
     *  @return array   ヘッダ, 本文
     */
    function _parse($mail)
    {
        list($header_line, $body) = preg_split('/\r?\n\r?\n/', $mail, 2);
        $header_line .= "\n";

        $header_lines = explode("\n", $header_line);
        $header = array();
        foreach ($header_lines as $h) {
            if (strstr($h, ':') == false) {
                continue;
            }
            list($key, $value) = preg_split('/\s*:\s*/', $h, 2);
            $i = strtolower($key);
            $header[$i] = array();
            $header[$i][] = $key;
            $header[$i][] = preg_replace('/([^\x00-\x7f]+)/e', "MailSender::encode_MIME('$1')", $value);
        }

        return array($header, $body);
    }

    /**
     *  メールを送信する
     *
     *  $attach の指定方法:
     *  - 既存のファイルを添付するとき
     *  <code>
     *  array('filename' => '/tmp/hoge.xls', 'content-type' => 'application/vnd.ms-excel')
     *  </code>
     *  - 文字列に名前を付けて添付するとき
     *  <code>
     *  array('name' => 'foo.txt', 'content' => 'this is foo.')
     *  </code>
     *  'content-type' 省略時は 'application/octet-stream' となる。
     *  複数添付するときは上の配列を添字0から始まるふつうの配列に入れる。
     *
     *  @access public
     *  @param  string  $to         メール送信先アドレス (nullのときは送信せずに内容を return する)
     *  @param  array   $param      テンプレートマクロ or $templateがMAILSENDER_TYPE_DIRECTのときはメール送信内容)
     *  @param  array   $attach     添付ファイル
     *  @return mixed  mail() 関数の戻り値 or メール内容 or メール情報
     */
    function send($to, $template, $macro, $attach = null)
    {
        // メール内容を作成
        $mail = '';
        if ($template === MAILSENDER_TYPE_DIRECT) {
            $mail = $macro;
        } else {
            $renderer =& $this->getTemplateEngine();

            // 基本情報設定
            $renderer->setProp("site_name", SITE_NAME);
            $renderer->setProp("site_url", SITE_URL);

            // デフォルトマクロ設定
            $macro = $this->_setDefaultMacro($macro);

            // ユーザ定義情報設定
            if (is_array($macro)) {
                foreach ($macro as $key => $value) {
                    $renderer->setProp($key, $value);
                }
            }
            if (isset($this->def[$template])) {
                $template = $this->def[$template];
            }
            $mail = $renderer->perform(sprintf('%s/%s', $this->mail_dir, $template), true);
        }
        if ($to === null) {
            // メール内容を戻す
            list($header_line, $body) = preg_split('/\r?\n\r?\n/', $mail, 2);
            $header_lines = explode("\n", $header_line);
            $header = array();
            foreach ($header_lines as $h) {
                if (strstr($h, ':') == false) {
                    continue;
                }
                list($key, $value) = preg_split('/\s*:\s*/', $h, 2);
                $i = strtolower($key);
                $header[$i] = $value;
            }
            $res = array('header' => $header, 'body' => $body);
            return $res;
        } else if (!MAIL_SEND || $to === 'view') {
            // メール表示
            pr($to);
            pr(htmlspecialchars($mail));
            return;
        }

        // メール内容をヘッダと本文に分離
        $mail = str_replace("\r\n", "\n", $mail);
        list($header, $body) = $this->_parse($mail);

        // ヘッダ
        if (isset($header['mime-version']) === false) {
            $header['mime-version'] = array('Mime-Version', '1.0');
        }
        if (isset($header['subject']) === false) {
            $header['subject'] = array('Subject', 'no subject in original');
        }
        if (isset($header['content-type']) === false) {
            $header['content-type'] = array(
                'Content-Type',
                $attach === null ? 'text/plain; charset=UTF-8'
                                 : "multipart/mixed; \n\tboundary=\"$boundary\"",
            );
        }
        // 改行コードを CRLF に
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $body = str_replace("\n", "\r\n", $body);
        }

        /**********
         * 送信
         **********/
        // sendmail送信
        $header_line = "";
        foreach ($header as $key => $value) {
            if ($key == 'subject') {
                // should be added by mail()
                continue;
            }
            if ($header_line != "") {
                $header_line .= "\n";
            }
            $header_line .= $value[0] . ": " . $value[1];
        }
        foreach (to_array($to) as $rcpt) {
            $res = mail($rcpt, $header['subject'][1], $body, $header_line);
            if (!$res) {
                break;
            }
        }
        return $res;
    }
}
?>