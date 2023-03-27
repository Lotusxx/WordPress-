<?php get_header(); ?>
<div class="top-cap">
    <img src="http://demo001.freemas.org/wp-content/uploads/2021/04/condominium-690086_1920-コピー.jpg" />
    <h1>不動産一覧</h1>
</div>
<main>
    <ul class="breadcrumb"><a href="<?php echo home_url(); ?>"><li>ホーム</li></a>><a href="<?php echo get_post_type_archive_link('realestate'); ?>"><li class="bold">物件一覧</li></a>><li class="bold"><?php the_title(); ?></li></ul>
    <?php if(have_posts()):while(have_posts()):the_post(); ?>
        <div class="img-area">

            <div class="main-img">
                <?php 
                    for( $i=0; $i < 10; $i++){
                        $realestate_image[] = get_post_meta(get_the_ID(),'realestate-image_'.$i,true);
                        if ( $realestate_image[$i] ) {
                            $realestate_full = wp_get_attachment_image_src ( $realestate_image[$i], 'full' );
                        ?>
                        <img src="<?php echo $realestate_full[0] ?>" width="<?php echo $realestate_full[1]; ?>" height="<?php echo $realestate_full[2]; ?>" class="main_image<?php echo $i ?>">
                        <?php 
                        }
                    } ?>
            </div>
                
            <div class="thumbnail-img">
            <?php
                for( $i=0; $i < 10; $i++){
                    $realestate_image[] = get_post_meta(get_the_ID(),'realestate-image_'.$i,true);
                    if ( $realestate_image[$i] ) {
                        $realestate_thumb = wp_get_attachment_image_src ( $realestate_image[$i], 'thumbnail' );
                    ?>
                    <img src="<?php echo $realestate_thumb[0] ?>" width="<?php echo $realestate_thumb[1]; ?>" height="<?php echo $realestate_thumb[2]; ?>" class="<?php echo $i ?>">
                    <?php 
                    }
                } ?>
            </div>
        </div>
        <div class="contents_area">
            <?php the_content();?>
        </div>
    <?php endwhile;endif; ?>
    <div class="realEstateInfo">
        <table>
            <tr><th>所在地</th><td><?php echo get_post_meta( get_the_ID(), 'info_address', true ) ?></td></tr>
            <tr><th>交通</th><td><?php echo get_post_meta( get_the_ID(), 'info_traffic', true ) ?></td></tr>
            <tr><th>総区画</th><td><?php echo get_post_meta( get_the_ID(), 'info_parcel', true ) ?></td></tr>
            <tr><th>用途地域</th><td><?php echo get_post_meta( get_the_ID(), 'info_use', true ) ?></td></tr>
            <tr><th>建ぺい率</th><td><?php echo get_post_meta( get_the_ID(), 'info_concealment', true ) ?></td></tr>
            <tr><th>容積率</th><td><?php echo get_post_meta( get_the_ID(), 'info_volume', true ) ?></td></tr>
            <tr><th>その他法令</th><td><?php echo get_post_meta( get_the_ID(), 'info_decree', true ) ?></td></tr>
            <tr><th>道路幅員</th><td><?php echo get_post_meta( get_the_ID(), 'info_width', true ) ?></td></tr>
            <tr><th>道路接幅員</th><td><?php echo get_post_meta( get_the_ID(), 'info_approach', true ) ?></td></tr>
            <tr><th>宅地面積</th><td><?php echo get_post_meta( get_the_ID(), 'info_residential', true ) ?></td></tr>
            <tr><th>建物面積</th><td><?php echo get_post_meta( get_the_ID(), 'info_building', true ) ?></td></tr>
        </table>
        <p>
            販売価格：<?php echo get_post_meta( get_the_ID(), 'info_value', true ) ?>
        </P>
    </div>
    <?php 
  $prev_post = get_previous_post();
  if( !empty( $prev_post ) ) {
    $prev_url = get_permalink( $prev_post->ID );
  }
?>
 <?php 
  $next_post = get_next_post();
  if( !empty( $next_post ) ) {
    $next_url = get_permalink( $next_post->ID );
  }
?>
<div class="singleLink">
  <?php if(!empty( $next_url )){ 
      echo "<a href='".$next_url."'>＜　前へ</a>";
    } ?> 
  | <?php if(!empty( $prev_url )){ 
      echo "<a href='".$prev_url."'>次へ　＞</a>";
    } ?>
</div>
</main>
<?php get_footer();?>