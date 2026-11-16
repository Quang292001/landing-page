<?php
/**
 * index.php — template dự phòng cuối cùng trong template hierarchy.
 * WordPress BẮT BUỘC theme có style.css + index.php. Trang chủ dùng front-page.php;
 * file này chỉ hiển thị các trang/bài viết khác (Chính sách, 404...) bằng Loop cơ bản.
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="top" class="wrap" style="padding-block:160px 96px">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="h2"><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="h2"><?php esc_html_e( 'Không tìm thấy nội dung.', 'moc-studio' ); ?></h1>
		<p><a class="link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Về trang chủ', 'moc-studio' ); ?> &nbsp;→</a></p>
	<?php endif; ?>
</main>

<?php
get_footer();
