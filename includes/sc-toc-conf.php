<?php

if (!defined('SCTOC_CONF')):
define('SCTOC_CONF', 1);

define('SCTOC_TXT_SHORTCODE', 'sc-toc');

define('SCTOC_TXT_TABLE_OF_CONTENTS', '目次');
define('SCTOC_TXT_READTIME', '読破時間');
define('SCTOC_TXT_DESIGN', 'デザイン');

define('SCTOC_TXT_UPDATE', '更新');
define('SCTOC_TXT_UPDATED', SCTOC_TXT_UPDATE . 'しました。');
define('SCTOC_TXT_CLEAR', 'リセット');
define('SCTOC_TXT_CLEARED', SCTOC_TXT_CLEAR . 'しました。');
define('SCTOC_TXT_SETTING', '設定');
define('SCTOC_TXT_SETTING_DESCTIPTION_TOC', '<p>固定ページや投稿ページの本文に表示する「目次」の設定を行います。<br>なお、目次の階層は2つまでとなります。</p><p>目次を表示するには、<input type="text" readonly value="[' . SCTOC_TXT_SHORTCODE . ']">　のショートコードを本文に張り付けてください。</p>');define('SCTOC_TXT_SETTING_DESCTIPTION_READTIME', '<p>固定ページや投稿ページの本文に表示する「読破時間」の設定を行います。<br>「読破時間」は「目次」の下に表示されます。</p><p>「読破時間」は「目次」ショートコードと共にデフォルトで表示されますが、不要な場合は<input type="text" readonly value="[' . SCTOC_TXT_SHORTCODE . ' readtime=0]">　のように目次ショートコードに「readtime=0」のパラメータを付与すると、読破時間は非表示となります。</p>');
define('SCTOC_TXT_SETTING_DESCTIPTION_DESIGN', '<p>「目次」および「読破時間」のデザインを変更する場合は、本プラグインの「assets」フォルダ内にある「sc-toc.css」ファイルを、ご利用中のテーマフォルダの中にコピーして編集することでデザインを変更することができます。</p>');


define('SCTOC_TXT_1ST_TAG_SETTING', '第一階層タグ');
define('SCTOC_TXT_2ND_TAG_SETTING', '第二階層タグ');
define('SCTOC_TXT_ANCHOR_ID_SETTING', 'アンカー用ID');
define('SCTOC_TXT_ANCHOR_ID_SETTING_MEMO', '* デフォルト／空欄の場合は、"%s"を使用します。');
define('SCTOC_TXT_READTIME_HTML_SETTING', '表示文字列');
define('SCTOC_TXT_READTIME_HTML_SETTING_MEMO', '* HTMLはspanタグのみ使用可能です。（除去されます）<br>* 読破時間は　単位：分　で計算されます。<br>* {readtime}キーは読破時間に変換して表示されます。');

define('SCTOC_TXT_VALIDATE_ERR_ANCHOR_ID', SCTOC_TXT_ANCHOR_ID_SETTING . 'は32文字以内でご入力ください。');

define('SCTOC_TXT_SELECT_NONE', '指定なし');

define('SCTOC_TXT_READTIME_KEY', '{readtime}');
define('SCTOC_TXT_READTIME_MSG', 'この記事は<span>約' . SCTOC_TXT_READTIME_KEY . '分</span>で読めます。');

define('SCTOC_TXT_PLUGINNAME', 'SC-TableOfContents');
define('SCTOC_TXT_PLUGINNAME_JP', SCTOC_TXT_TABLE_OF_CONTENTS . '＆' . SCTOC_TXT_READTIME);


endif;
