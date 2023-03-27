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

    //nonce発行
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
            <tr class="form-field">
                <th scope="row">交通</th><td><input type="text" id="info_traffic" name="info_traffic" value="<?php echo get_post_meta( get_the_ID(), 'info_traffic', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">総区画</th><td><input type="text" id="info_parcel" name="info_parcel" value="<?php echo get_post_meta( get_the_ID(), 'info_parcel', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">用途地域</th><td><input type="text" id="info_use" name="info_use" value="<?php echo get_post_meta( get_the_ID(), 'info_use', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">隠ぺい率</th><td><input type="text" id="info_concealment" name="info_concealment" value="<?php echo get_post_meta( get_the_ID(), 'info_concealment', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">容積率</th><td><input type="text" id="info_volume" name="info_volume" value="<?php echo get_post_meta( get_the_ID(), 'info_volume', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">その他法令</th><td><input type="text" id="info_decree" name="info_decree" value="<?php echo get_post_meta( get_the_ID(), 'info_decree', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">道路幅員</th><td><input type="text" id="info_width" name="info_width" value="<?php echo get_post_meta( get_the_ID(), 'info_width', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">道路接道幅</th><td><input type="text" id="info_approach" name="info_approach" value="<?php echo get_post_meta( get_the_ID(), 'info_approach', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">宅地面積</th><td><input type="text" id="info_residential" name="info_residential" value="<?php echo get_post_meta( get_the_ID(), 'info_residential', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">建物面積</th><td><input type="text" id="info_building" name="info_building" value="<?php echo get_post_meta( get_the_ID(), 'info_building', true ) ?>"></td>
            </tr>
            <tr class="form-field">
                <th scope="row">販売価格</th><td><input type="text" id="info_value" name="info_value" value="<?php echo get_post_meta( get_the_ID(), 'info_value', true ) ?>"></td>
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
            if( isset( $_POST['info_traffic'] ) ) {
                if( $_POST['info_traffic'] !== '' ) {
                    update_post_meta( $post_id, 'info_traffic', $_POST['info_traffic'] );
                } else {
                    delete_post_meta( $post_id, 'info_traffic' );
                    }
            }
            if( isset( $_POST['info_parcel'] ) ) {
                if( $_POST['info_parcel'] !== '' ) {
                    update_post_meta( $post_id, 'info_parcel', $_POST['info_parcel'] );
                } else {
                    delete_post_meta( $post_id, 'info_parcel' );
                    }
            }
            if( isset( $_POST['info_use'] ) ) {
                if( $_POST['info_use'] !== '' ) {
                    update_post_meta( $post_id, 'info_use', $_POST['info_use'] );
                } else {
                    delete_post_meta( $post_id, 'info_use' );
                    }
            }
            if( isset( $_POST['info_concealment'] ) ) {
                if( $_POST['info_concealment'] !== '' ) {
                    update_post_meta( $post_id, 'info_concealment', $_POST['info_concealment'] );
                } else {
                    delete_post_meta( $post_id, 'info_concealment' );
                    }
            }
            if( isset( $_POST['info_volume'] ) ) {
                if( $_POST['info_volume'] !== '' ) {
                    update_post_meta( $post_id, 'info_volume', $_POST['info_volume'] );
                } else {
                    delete_post_meta( $post_id, 'info_volume' );
                    }
            }
            if( isset( $_POST['info_decree'] ) ) {
                if( $_POST['info_decree'] !== '' ) {
                    update_post_meta( $post_id, 'info_decree', $_POST['info_decree'] );
                } else {
                    delete_post_meta( $post_id, 'info_decree' );
                    }
            }
            if( isset( $_POST['info_width'] ) ) {
                if( $_POST['info_width'] !== '' ) {
                    update_post_meta( $post_id, 'info_width', $_POST['info_width'] );
                } else {
                    delete_post_meta( $post_id, 'info_width' );
                    }
            }
            if( isset( $_POST['info_approach'] ) ) {
                if( $_POST['info_approach'] !== '' ) {
                    update_post_meta( $post_id, 'info_approach', $_POST['info_approach'] );
                } else {
                    delete_post_meta( $post_id, 'info_approach' );
                    }
            }
            if( isset( $_POST['info_residential'] ) ) {
                if( $_POST['info_residential'] !== '' ) {
                    update_post_meta( $post_id, 'info_residential', $_POST['info_residential'] );
                } else {
                    delete_post_meta( $post_id, 'info_residential' );
                    }
            }
            if( isset( $_POST['info_building'] ) ) {
                if( $_POST['info_building'] !== '' ) {
                    update_post_meta( $post_id, 'info_building', $_POST['info_building'] );
                } else {
                    delete_post_meta( $post_id, 'info_building' );
                    }
            }
            if( isset( $_POST['info_value'] ) ) {
                if( $_POST['info_value'] !== '' ) {
                    update_post_meta( $post_id, 'info_value', $_POST['info_value'] );
                } else {
                    delete_post_meta( $post_id, 'info_value' );
                    }
            }
        }
     }
}
?>