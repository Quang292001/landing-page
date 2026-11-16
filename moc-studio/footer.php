<?php
/**
 * footer.php — phần cuối dùng chung: footer, popup xem ảnh, nút liên hệ nổi.
 * Gọi bằng get_footer() ở cuối mỗi template.
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$moc_links  = moc_studio_get_contact_links();
$moc_social = array(
	'Instagram' => $moc_links['instagram'],
	'Facebook'  => $moc_links['facebook'],
	'Behance'   => $moc_links['behance'],
	'Zalo'      => $moc_links['zalo'],
	'TikTok'    => $moc_links['tiktok'],
);
?>

<footer class="foot">
	<div class="wrap">
		<div class="top">
			<div>
				<p class="mark"><?php echo esc_html( moc_studio_site_name_upper() ); ?></p>
				<p class="tag"><?php esc_html_e( 'Một khoảnh khắc. Một đời nhớ.', 'moc-studio' ); ?></p>
			</div>
			<nav class="soc" aria-label="<?php esc_attr_e( 'Mạng xã hội', 'moc-studio' ); ?>">
				<?php
				foreach ( $moc_social as $moc_name => $moc_url ) {
					if ( $moc_url ) { // Bỏ qua mạng xã hội chưa điền link trong Customizer.
						printf(
							'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
							esc_url( $moc_url ),
							esc_html( $moc_name )
						);
					}
				}
				?>
			</nav>
		</div>
		<div class="bot">
			<span>
				<?php
				/* translators: 1: năm hiện tại, 2: tên trang. */
				printf( esc_html__( '© %1$s %2$s. Giữ lại những điều đáng nhớ.', 'moc-studio' ), esc_html( wp_date( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
				?>
			</span>
			<span><?php esc_html_e( 'CỦ CHI, VIETNAM', 'moc-studio' ); ?> &nbsp;·&nbsp; <?php esc_html_e( 'BY APPOINTMENT', 'moc-studio' ); ?></span>
		</div>
	</div>
</footer>

<dialog class="lb" id="lightbox" aria-labelledby="lb-title">
	<div class="lb-inner">
		<header class="lb-head">
			<div>
				<p class="lb-meta"></p>
				<h3 class="lb-title" id="lb-title"></h3>
			</div>
			<p class="lb-count" aria-live="polite"></p>
			<button class="lb-close" type="button" aria-label="<?php esc_attr_e( 'Đóng', 'moc-studio' ); ?>">&times;</button>
		</header>
		<div class="lb-stage">
			<button class="lb-nav lb-prev" type="button" aria-label="<?php esc_attr_e( 'Ảnh trước', 'moc-studio' ); ?>">&larr;</button>
			<img class="lb-img" alt="">
			<button class="lb-nav lb-next" type="button" aria-label="<?php esc_attr_e( 'Ảnh sau', 'moc-studio' ); ?>">&rarr;</button>
		</div>
		<div class="lb-thumbs"></div>
		<footer class="lb-foot">
			<a class="btn lb-book" href="<?php echo esc_url( moc_studio_anchor( 'dat-lich' ) ); ?>"><?php esc_html_e( 'Đặt buổi chụp như thế này', 'moc-studio' ); ?> &nbsp;→</a>
			<a class="link lb-more" data-gallery href="#"><?php esc_html_e( 'Xem thêm ảnh trên TikTok', 'moc-studio' ); ?> &nbsp;→</a>
		</footer>
	</div>
</dialog>

<div class="float" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'moc-studio' ); ?>">
	<a class="z" data-zalo data-tip="<?php esc_attr_e( 'Nhắn Zalo cho Mộc', 'moc-studio' ); ?>" href="#" aria-label="<?php esc_attr_e( 'Nhắn Zalo', 'moc-studio' ); ?>"><svg class="ic" width="24" height="24"><use href="#i-chat"/></svg></a>
	<a class="t" data-tiktok data-tip="<?php esc_attr_e( 'TikTok của Mộc', 'moc-studio' ); ?>" href="#" aria-label="<?php esc_attr_e( 'Xem TikTok', 'moc-studio' ); ?>"><svg class="ic" width="24" height="24"><use href="#i-note"/></svg></a>
</div>

<?php
// wp_footer() BẮT BUỘC: WP in các <script> nạp ở footer (main.js, jQuery...) và admin bar tại đây.
wp_footer();
?>
</body>
</html>
