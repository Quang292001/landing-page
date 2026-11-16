<?php
/**
 * header.php — phần đầu dùng chung cho mọi trang.
 * Gọi bằng get_header() ở đầu mỗi template.
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
// wp_head() BẮT BUỘC: WP in <title>, CSS/JS đã enqueue, SEO plugin, admin bar... tại đây.
wp_head();
?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="progress" aria-hidden="true"></div>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
	<symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-12.6 7.4L3 20.5l1.7-4.9A8.5 8.5 0 1 1 21 11.5Z"/></symbol>
	<symbol id="i-note" viewBox="0 0 24 24"><path d="M14 3v11.2a3.8 3.8 0 1 1-3.8-3.8"/><path d="M14 3c.4 2.6 2 4.2 4.8 4.4"/></symbol>
</svg>

<header class="nav">
	<div class="in">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="logo" href="<?php echo esc_url( moc_studio_anchor( 'top' ) ); ?>"><?php echo esc_html( moc_studio_site_name_upper() ); ?></a>
		<?php endif; ?>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '<ul>%3$s</ul>', // Giữ đúng cấu trúc <ul> mà CSS .nav ul đang dùng.
				'depth'          => 1,
				'fallback_cb'    => 'moc_studio_default_menu',
			)
		);
		?>

		<a class="cta" href="<?php echo esc_url( moc_studio_anchor( 'dat-lich' ) ); ?>"><?php esc_html_e( 'ĐẶT LỊCH CHỤP', 'moc-studio' ); ?></a>
	</div>
</header>
