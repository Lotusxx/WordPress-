<?php get_header(); ?>
<div class="top-cap">
    <img src="http://demo001.freemas.org/wp-content/uploads/2021/04/condominium-690086_1920-コピー.jpg" />
    <h1>不動産一覧</h1>
</div>
    <main>
        <ul class="breadcrumb"><a href="<?php home_url(); ?>"><li>ホーム</li></a>　><li class="bold">物件一覧</li></ul>
        <div class="flex mt-10">
            <div class="grid3-3">
            <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
                $args = array(
                    'posts_per_page'=>6,
                    'post_type'=>'realestate',
                    'paged' => $paged
                );
                $posts = get_posts($args);
                foreach($posts as $post):
                    setup_postdata($post); //記事データ
            ?>
            <div class="realestate-link">
                    <a href="<?php the_permalink(); ?>">
                        <?php if(has_post_thumbnail()): ?>
                            <div class="trim">
                                <img src="<?php echo get_the_post_thumbnail_url(); ?>">
                            </div>
                        <?php else: ?>
                            <div class="trim">
                                <img src="https://placehold.jp/600x400.png">
                            </div>
                        <?php endif; ?>
                        <p>詳細を見る</p>
                    </a>
            </div>
            <?php endforeach;  ?>
            </div>
        </div>
        <div>
        <?php
            $args2 = array(
                'mid_size' => 1,
                'prev_text' => '&lt;&lt;前へ',
                'next_text' => '次へ&gt;&gt;',
                'screen_reader_text' => ' ',
            );
            the_posts_pagination($args2);
            wp_reset_postdata();
        ?>
        </div>
    </main>
<?php get_footer();?>