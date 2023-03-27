<?php
/*
  Plugin Name: Real Estate Pager
  Plugin URI:
  Description: 不動産の紹介ページをカスタマイズする。他の用途にも使用可能
  Version: 1.0.0
  Author: HIROKI KANDA
  Author URI: 
  License: GPLv2
 */

 //add_action群
add_action( 'init', 'create_post_type' );

//不動産カスタム投稿タイプ作成
function create_post_type() {
    $supports = array(
        'title',
        'editor',
        'thumbnail',
    );

    $labels = array(
        'menu_name' => '不動産情報',
        'add_new' => '新規不動産登録',
        'add_new_item' => '新規不動産を登録',
        'edit_item' => '不動産情報を編集',
        'view_item' => '不動産情報を表示',
        'search_items' => '不動産を検索',
        'not_found' => '不動産が見つかりませんでした',
        'not_found_in_trash' => 'ゴミ箱に不動産はありません'
    );

    register_post_type(
        'realestate', // カスタム投稿名
        array(
            'label' => '不動産情報管理', // メニュー名
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'rewrite' => true,
            'query_var' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
            'rest_base' => 'realestate',
            'has_archive' => true, // アーカイブを有効
            'menu_position' => 5, // 「投稿」の下に配置
            'supports' => $supports, // 投稿画面module設定
            'labels' => $labels // 管理画面の表示文言
        )
    );
}

?>