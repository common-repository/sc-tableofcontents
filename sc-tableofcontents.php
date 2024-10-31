<?php
/*
Plugin Name: SC-TableOfContents -目次＆読破時間-
Plugin URI: 
Description: 投稿と固定ページの本文内の好きな位置に、目次と読破時間を表示できます。JSを使用しないのでサイトの作りにも影響しません。This plugin provides a shortcode to display the table of contents and reading time on all posts and pages displayed on the_content.
Version: 1.0.0
Author: seo-kk
Author URI: https://seo-kk.jp
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) exit;

if ($files = glob(dirname(__FILE__).'/includes/*.php')) {
    foreach ($files as $file) {
        include_once($file);
    }
}

$sctoc = new SC_TableOfContents();
