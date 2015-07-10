<?php
/**
 *  smarty_function:image_table()
 *
 *  @param  array   $params  引数
 *  @param  string  $smarty  Smartyオブジェクト
 *  @return string  フォーマット済み文字列
 */
function smarty_function_image_table($params, &$smarty)
{
    if (isset($params['id']) === false) {
        return '';
    }
    $data = $params['data'];
    $length = count($data);
    $style = '';
    if ($length === 0) {
         $style = ' style="display: none;"';
    }
    $noteBase = '';
    if (isset($params['note']) && $params['note']) {
        $noteBase = "      <span class=\"item\">備考</span>";
        if (isset($params['confirm']) && $params['confirm']) {
            $noteBase .= "<span class=\"value\">%s</span>\n";
        } else {
            $noteBase .= sprintf("<input type=\"text\" name=\"note[%s][]\" value=\"%%s\" size=\"18\" />\n", $params['name']);
        }
    }
    $tag = '';
    if (isset($params['confirm']) && $params['confirm'] && $length == 0) {
        $tag .= "  画像は登録されていません</td>";
    } else {
        if (isset($params['confirm']) && $params['confirm']) {
            $tag .= "<table class=\"dynamicTableImage\" summary=\" \">\n";
        } else {
            $tag .= sprintf("<table class=\"dynamicTableImage mt10\" id=\"%s\"%s summary=\" \">\n",
                            $params['id'],
                            $style
                           );
            if ($length == 0) {
                $note = sprintf($noteBase, '');
                $tag .= "  <tr>" .
                        "    <th style=\"width: 14%;\">画像0</th>" .
                        "    <td class=\"cellImg\">\n" .
                        $note .
                        "    </td>" .
                        "    <td class=\"cellCtrl\">" .
                        "      <input type=\"button\" value=\"▲\" id=\"dynamic_up_row_0_image\" />\n" .
                        "      <input type=\"button\" value=\"▼\" id=\"dynamic_down_row_0_image\" />\n" .
                        "      <input type=\"button\" class=\"mt15\" value=\"削除\" id=\"dynamic_del_row_0_image\" />\n" .
                        "    </td>" .
                        "  </tr>";
            }
        }
        if ($length > 0) {
            $count = 1;
            foreach ($data as $key => $img) {
                if (isset($img['small']['url'])) {
                    $smallImg = $img['small'];
                    $largeUrl = $img['large']['url'];
                    if (isset($img['original']['url'])) {
                        $largeUrl = $img['original']['url'];
                    }
                    $upDisable = $count === 1 ? ' disabled="disabled"' : '';
                    $downDisable = $count === $length ? '' :' disabled="disabled"';
                    // 備考欄
                    if (isset($params['note']) && $params['note']) {
                        if (isset($data[$key]['note'])) {
                            $note = sprintf($noteBase, htmlspecialchars($data[$key]['note']));
                        } else {
                            $note = sprintf($noteBase, '');
                        }
                    }
                    if (isset($params['confirm']) && $params['confirm']) {
                        $tag .= sprintf("  <tr%06\$s>\n" .
                                        "    <th style=\"width: 14%%;\">画像%1\$d</th>\n" .
                                        "    <td class=\"cellImg\">\n" .
                                        "      <a href=\"%2\$s\" rel=\"lightbox\"><img src=\"%3\$s\" width=\"%4\$d\" height=\"%5\$d\" /></a>\n" .
                                        $note .
                                        "    </td>\n" .
                                        "  </tr>\n",
                                        $count,
                                        $largeUrl,
                                        $smallImg['url'],
                                        $smallImg['width'],
                                        $smallImg['height'],
                                        $key % 2 ? ' class="line2"' : ''
                                       );
                    } else {
                        $tag .= sprintf("  <tr%09\$s>\n" .
                                        "    <th style=\"width: 14%%;\">画像%1\$d</th>\n" .
                                        "    <td class=\"cellImg\">\n" .
                                        "      <a href=\"%2\$s\" rel=\"lightbox\"><img src=\"%3\$s\" width=\"%4\$d\" height=\"%5\$d\" /></a>\n" .
                                        $note .
                                        "    </td>\n" .
                                        "    <td class=\"cellCtrl\">\n" .
                                        "      <input type=\"button\" value=\"▲\"%6\$s id=\"dynamic_up_row_%8\$d_image\" />\n" .
                                        "      <input type=\"button\" value=\"▼\"%7\$s id=\"dynamic_down_row_%8\$d_image\" />\n" .
                                        "      <input type=\"button\" class=\"mt15\" value=\"削除\" id=\"dynamic_del_row_%8\$d_image\" />\n" .
                                        "    </td>\n" .
                                        "  </tr>\n",
                                        $count,
                                        $largeUrl,
                                        $smallImg['url'],
                                        $smallImg['width'],
                                        $smallImg['height'],
                                        $upDisable,
                                        $downDisable,
                                        $key,
                                        $key % 2 ? ' class="line2"' : ''
                                       );
                    }
                    $count++;
                }
            }
        }
        $tag .= "</table>";
    }
    return $tag;
}
?>
