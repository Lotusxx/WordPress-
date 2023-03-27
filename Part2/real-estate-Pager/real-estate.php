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

 //グローバル変数宣言
 $maxImg = 10;

 //add_action群
add_action( 'init', 'create_post_type' );
add_action( 'admin_menu', 'add_custom_fields' );
add_action( 'admin_enqueue_scripts', 'add_api' );
add_action( 'admin_footer', 'add_script' );
add_action( 'save_post_realestate', 'save_realestate' );
add_action( 'save_post_realestate', 'save_realestate_info' );

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

//カスタムフィールドの作成
function add_custom_fields() {
    add_meta_box(
        'realestate_sectionid', //id
        '不動産画像の登録',//管理画面の見出し
        'realestate_custom_fields',//  管理画面表示用関数
        'realestate',//カスタムフィールドを表示する投稿名
        'advanced',// 編集画面セクションが表示される部分
        'default',//優先順位
    );

    add_meta_box(
        'realestate_infoid', //id
        '不動産情報の登録',//管理画面の見出し
        'realestate_info_fields',//  管理画面表示用関数
        'realestate',//カスタムフィールドを表示する投稿名
        'advanced',// 編集画面セクションが表示される部分
        'default',//優先順位
    );
}

//カスタムフィールド表示HTMLの作成
function realestate_custom_fields() {
    $realestate_image = array();
    global $maxImg;

    // 画像の格納（数はMAX10でTOPで宣言）
    for ( $i=0; $i < $maxImg; $i++ ) {
        $realestate_image[] = get_post_meta( get_the_ID(), 'realestate-image_'.$i, true );
    }

    //nonce発行(下記wp-nonceは本番では書き換えること)
    wp_nonce_field('wp-nonce-key', 'real_estate_nonce');
?>
    <!-- 画面の出力 -->
    <table class="form-table">
        <?php for ( $i=0; $i < $maxImg; $i++ ):?>
            <tr class="form-field">
                <th scope="row">不動産画像<?php echo $i+1 ?></th>
                <td>
                    <input type="hidden" id="realestate-image_<?php echo $i; ?>" name="realestate-image_<?php echo $i; ?>" value="<?php echo $realestate_image[$i] ? $realestate_image[$i] : '' ?>">
                    <div id="image-wrapper_<?php echo $i?>">
                    <?php if ( $realestate_image[$i] ) {
                        $realestate_thumb = wp_get_attachment_image_src ( $realestate_image[$i], 'thumbnail' );
                        ?>
                        <img src="<?php echo $realestate_thumb[0] ?>" width="<?php echo $realestate_thumb[1]; ?>" height="<?php echo $realestate_thumb[2]; ?>" class="custom_media_image">
                    <?php } ?>
                    </div>
                    <p><input type="button" class="button button-secondary media_button" name="media_button" value="追加" id="media-button_<?php echo $i?>" />
                    <input type="button" class="button button-secondary media_remove" name="media_remove" value="削除" id="media-remove_<?php echo $i?>"/></p>
                </td>
            </tr>
        <?php endfor;?>
    </table>
<?php
}

//メディアの追加っぽく画像を選べるAPI呼び出し
function add_api() {
    wp_enqueue_media();
}

//画面下部に画像出力のためのjQuery追加
function add_script() {
    // jsファイルを追加
    wp_enqueue_script( 'my_admin_script', plugins_url() . '/real-estate-Pager/js/realestate-img-adjustment.js' );
}

//画像の保存処理
function save_realestate( $post_id ) {
    global $maxImg;

     //nonce値の確認
     if ( isset($_POST['real_estate_nonce']) && $_POST['real_estate_nonce'] ) {
        if ( check_admin_referer('wp-nonce-key', 'real_estate_nonce') ) {
            //ループで一括保存
            for ( $i=0; $i < $maxImg; $i++ ) {
                // 追加ボタンで追加したものは登録し、削除したものはメタデータからも削除する
                if( isset( $_POST['realestate-image_' . $i] ) ) {
                    if( $_POST['realestate-image_' . $i] !== '' ) {
                        update_post_meta( $post_id, 'realestate-image_' . $i, $_POST['realestate-image_' . $i] );
                    } else {
                        delete_post_meta( $post_id, 'realestate-image_'.$i );
                        }
                }
            }
        }
     }
}

//不動産情報のカスタムフィールド表示HTMLの作成
function realestate_info_fields() {
    //nonce発行
    wp_nonce_field('wp-nonce-key', 'real_estate_nonce');
?>
    <!-- 画面の出力 -->
    <table class="form-table">
            <tr class="form-field">
                <th scope="row">所在地</th><td><input type="text" id="info_address" name="info_address" value="<?php echo get_post_meta( get_the_ID(), 'info_address', true ) ?>"></td>
            </tr>
    </table>
<?php
}

//不動産情報の保存処理
function save_realestate_info( $post_id ) {
     //nonce値の確認
     if ( isset($_POST['real_estate_nonce']) && $_POST['real_estate_nonce'] ) {
        if ( check_admin_referer('wp-nonce-key', 'real_estate_nonce') ) {

            // 情報の登録
            if( isset( $_POST['info_address'] ) ) {
                if( $_POST['info_address'] !== '' ) {
                    update_post_meta( $post_id, 'info_address', $_POST['info_address'] );
                } else {
                    delete_post_meta( $post_id, 'info_address' );
                    }
            }
        }
     }
}
?>