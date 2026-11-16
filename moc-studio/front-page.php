<?php
/**
 * front-page.php — template trang chủ.
 *
 * Theo "template hierarchy", trang chủ ưu tiên front-page.php trước home.php
 * và index.php, nên landing page nằm ở đây. Phần động lấy từ:
 *   - moc_studio_get_albums()   → CPT "Bộ ảnh"   (inc/album-cpt.php)
 *   - moc_studio_get_packages() → Customizer     (inc/customizer.php)
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$moc_albums   = moc_studio_get_albums();
$moc_packages = moc_studio_get_packages();

// Lựa chọn trong form = loại chụp của các gói + "Khác", bỏ trùng.
$moc_services   = array_unique( array_filter( wp_list_pluck( $moc_packages, 'service' ) ) );
$moc_services[] = __( 'Khác', 'moc-studio' );
?>

<main id="top">
	<section class="hero">
		<div class="wrap grid">
			<div class="left">
				<p class="eyebrow"><?php esc_html_e( 'STUDIO NHIẾP ẢNH', 'moc-studio' ); ?> &nbsp;/&nbsp; <?php esc_html_e( 'CỦ CHI', 'moc-studio' ); ?></p>
				<h1><?php echo wp_kses( __( 'Chụp một <br>khoảnh khắc.<br>Lưu một đời<br>kỷ niệm.', 'moc-studio' ), array( 'br' => array() ) ); ?></h1>
				<p class="desc"><?php echo wp_kses( __( 'Những bức ảnh đẹp nhất không cần quá cầu kỳ.<br>Chỉ cần bạn, thật tự nhiên, trong khoảnh khắc của mình.', 'moc-studio' ), array( 'br' => array() ) ); ?></p>
				<div class="actions">
					<a class="btn" href="#dat-lich"><?php esc_html_e( 'Đặt buổi chụp của bạn', 'moc-studio' ); ?> &nbsp;→</a>
					<a class="link" style="font-weight:400" href="#bo-anh"><?php esc_html_e( 'Khám phá bộ ảnh', 'moc-studio' ); ?> &nbsp;→</a>
				</div>
				<p class="edition"><?php esc_html_e( 'CHẬM LẠI MỘT NHỊP.', 'moc-studio' ); ?> &nbsp;<?php esc_html_e( 'CẢM NHẬN NHIỀU HƠN.', 'moc-studio' ); ?></p>
			</div>
			<div class="right">
				<div class="ph"><img src="<?php echo esc_url( moc_studio_img( 'EDEN.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Chân dung ngoại cảnh dưới ánh sáng tự nhiên', 'moc-studio' ); ?>" width="650" height="728" fetchpriority="high"></div>
				<p class="cap"><span><?php esc_html_e( '01 / NHỮNG NGÀY THẬT ĐẸP', 'moc-studio' ); ?></span><span><?php esc_html_e( 'Củ Chi, Việt Nam', 'moc-studio' ); ?></span></p>
			</div>
		</div>
	</section>

	<section class="values">
		<div class="wrap">
			<div class="row">
				<div class="item"><span class="idx">01</span><span class="v"><?php esc_html_e( 'Ánh sáng tự nhiên', 'moc-studio' ); ?></span></div>
				<div class="item"><span class="idx">02</span><span class="v"><?php esc_html_e( 'Cảm xúc chân thật', 'moc-studio' ); ?></span></div>
				<div class="item"><span class="idx">03</span><span class="v"><?php esc_html_e( 'Màu ảnh vượt thời gian', 'moc-studio' ); ?></span></div>
			</div>
		</div>
	</section>

	<section class="stories" id="bo-anh">
		<div class="wrap">
			<div class="head">
				<div>
					<p class="label"><?php esc_html_e( 'Những câu chuyện được lưu giữ', 'moc-studio' ); ?></p>
					<h2 class="h2"><?php esc_html_e( 'Mỗi khung hình, một câu chuyện.', 'moc-studio' ); ?></h2>
				</div>
				<a class="link more" style="font-weight:400" data-gallery href="#bo-anh"><?php esc_html_e( 'Xem tất cả bộ ảnh', 'moc-studio' ); ?> &nbsp;→</a>
			</div>
			<div class="cards">
				<?php foreach ( $moc_albums as $moc_album ) : ?>
					<article class="card" data-album="<?php echo esc_attr( $moc_album['key'] ); ?>">
						<div class="ph">
							<?php
							// 'cover' là HTML <img> do wp_get_attachment_image() sinh ra (đã escape từng thuộc tính),
							// hoặc ảnh mẫu đã escape trong moc_studio_default_albums(). Không bọc wp_kses_post()
							// vì nó lọc mất srcset/sizes/decoding → mất ảnh responsive.
							echo $moc_album['cover']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<span class="card-count"></span>
						</div>
						<h3><?php echo esc_html( $moc_album['title'] ); ?></h3>
						<p><?php echo esc_html( $moc_album['meta'] ); ?></p>
						<?php /* translators: %s: tên bộ ảnh. */ ?>
						<button class="card-hit" type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Xem bộ ảnh %s', 'moc-studio' ), $moc_album['title'] ) ); ?>"></button>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="about" id="moc">
		<div class="wrap">
			<div class="ph"><img src="<?php echo esc_url( moc_studio_img( 'studio.avif' ) ); ?>" alt="<?php esc_attr_e( 'Không gian bên trong Mộc Studio', 'moc-studio' ); ?>" loading="lazy"></div>
			<div class="txt">
				<p class="label"><?php esc_html_e( 'Mộc — từ những điều giản dị', 'moc-studio' ); ?></p>
				<h2 class="h2"><?php echo wp_kses( __( 'Không chỉ chụp ảnh.<br>Mộc lưu lại cảm xúc.', 'moc-studio' ), array( 'br' => array() ) ); ?></h2>
				<div class="p">
					<p><?php esc_html_e( 'Một nụ cười bất chợt. Một cái ôm thật lâu. Một phiên bản của bạn mà chính bạn cũng muốn giữ lại.', 'moc-studio' ); ?></p>
					<p><?php esc_html_e( 'Mộc tin rằng vẻ đẹp luôn có sẵn trong những điều tự nhiên. Chúng mình ở đây để lắng nghe, đồng hành và kể câu chuyện của bạn bằng ánh sáng.', 'moc-studio' ); ?></p>
				</div>
				<a class="link" href="#moc"><?php esc_html_e( 'Gặp gỡ Mộc Studio', 'moc-studio' ); ?> &nbsp;→</a>
			</div>
		</div>
	</section>

	<section class="services" id="dich-vu">
		<div class="wrap">
			<p class="label"><?php esc_html_e( 'Một buổi chụp dành riêng cho bạn', 'moc-studio' ); ?></p>
			<h2 class="h2"><?php esc_html_e( 'Bạn muốn lưu lại điều gì?', 'moc-studio' ); ?></h2>
			<p class="note"><?php esc_html_e( 'Gói chụp tham khảo · Có thể tùy chỉnh theo câu chuyện của bạn', 'moc-studio' ); ?></p>
			<div class="pkgs">
				<?php foreach ( $moc_packages as $moc_pkg ) : ?>
					<article class="pkg">
						<p class="k"><?php echo esc_html( $moc_pkg['label'] ); ?></p>
						<h3><?php echo esc_html( $moc_pkg['title'] ); ?></h3>
						<p class="price"><?php echo esc_html( $moc_pkg['price'] ); ?></p>
						<p class="d"><?php echo esc_html( $moc_pkg['desc'] ); ?></p>
						<hr>
						<?php if ( $moc_pkg['features'] ) : ?>
							<ul>
								<?php foreach ( $moc_pkg['features'] as $moc_feature ) : ?>
									<li><?php echo esc_html( $moc_feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<a class="link" href="#dat-lich"><?php esc_html_e( 'Tìm hiểu gói chụp', 'moc-studio' ); ?> &nbsp;→</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="process">
		<div class="wrap">
			<h2><?php esc_html_e( 'Thật nhẹ nhàng, từ lần đầu gặp.', 'moc-studio' ); ?></h2>
			<div class="steps">
				<div class="step"><span class="n">01</span><div><h3><?php esc_html_e( 'Kể Mộc nghe', 'moc-studio' ); ?></h3><p><?php echo wp_kses( __( 'Chia sẻ ý tưởng và điều bạn muốn giữ lại.<br>Mộc cùng bạn chọn concept phù hợp.', 'moc-studio' ), array( 'br' => array() ) ); ?></p></div></div>
				<div class="step"><span class="n">02</span><div><h3><?php esc_html_e( 'Cứ là bạn thôi', 'moc-studio' ); ?></h3><p><?php echo wp_kses( __( 'Đến buổi chụp với tâm trạng thoải mái.<br>Ánh sáng và góc máy, cứ để Mộc lo.', 'moc-studio' ), array( 'br' => array() ) ); ?></p></div></div>
				<div class="step"><span class="n">03</span><div><h3><?php esc_html_e( 'Nhận lại kỷ niệm', 'moc-studio' ); ?></h3><p><?php echo wp_kses( __( 'Chọn những khoảnh khắc yêu thích.<br>Nhận bộ ảnh hoàn thiện sau 7–10 ngày.', 'moc-studio' ), array( 'br' => array() ) ); ?></p></div></div>
			</div>
		</div>
	</section>

	<section class="kind">
		<div class="wrap">
			<p class="label"><?php esc_html_e( 'Một lời nhắn gửi Mộc', 'moc-studio' ); ?></p>
			<blockquote><?php esc_html_e( '“Mình vốn rất ngại ống kính. Nhưng hôm ấy, mọi thứ tự nhiên như một buổi dạo chơi. Nhìn ảnh, mình thấy đúng là chúng mình.”', 'moc-studio' ); ?></blockquote>
			<p class="who"><?php esc_html_e( 'AN & MINH', 'moc-studio' ); ?> &nbsp;/&nbsp; <?php esc_html_e( 'BỘ ẢNH CẶP ĐÔI', 'moc-studio' ); ?></p>
			<p class="cap"><?php esc_html_e( 'Câu chuyện minh họa cho Mộc Studio', 'moc-studio' ); ?></p>
		</div>
	</section>

	<section class="booking" id="dat-lich">
		<div class="wrap">
			<div>
				<p class="label"><?php esc_html_e( 'Hẹn bạn một ngày thật đẹp', 'moc-studio' ); ?></p>
				<h2><?php echo wp_kses( __( 'Câu chuyện tiếp theo,<br>là của bạn.', 'moc-studio' ), array( 'br' => array() ) ); ?></h2>
				<p class="desc"><?php esc_html_e( 'Để lại một lời nhắn. Mộc sẽ cùng bạn lên ý tưởng cho một buổi chụp thật riêng, thật là bạn.', 'moc-studio' ); ?></p>
				<div class="direct">
					<p class="t"><?php esc_html_e( 'Hoặc nhắn trực tiếp cho Mộc', 'moc-studio' ); ?></p>
					<div class="row">
						<a class="chat" data-zalo href="#"><svg class="ic" width="20" height="20"><use href="#i-chat"/></svg><?php esc_html_e( 'Nhắn qua Zalo', 'moc-studio' ); ?></a>
						<a class="chat" data-tiktok href="#"><svg class="ic" width="20" height="20"><use href="#i-note"/></svg><?php esc_html_e( 'Xem TikTok', 'moc-studio' ); ?></a>
					</div>
				</div>
				<p class="contact"><?php esc_html_e( 'Tư vấn concept miễn phí · Không cần đặt cọc ngay', 'moc-studio' ); ?></p>
			</div>
			<form id="booking-form" novalidate>
				<label class="field"><span><?php esc_html_e( 'Tên của bạn', 'moc-studio' ); ?></span><input type="text" name="name" autocomplete="name" required></label>
				<label class="field"><span><?php esc_html_e( 'Số điện thoại / Email', 'moc-studio' ); ?></span><input type="text" name="contact" autocomplete="email" required></label>
				<label class="field"><span><?php esc_html_e( 'Bạn muốn chụp gì?', 'moc-studio' ); ?></span>
					<select name="service" required>
						<option value="" selected disabled></option>
						<?php foreach ( $moc_services as $moc_service ) : ?>
							<option><?php echo esc_html( $moc_service ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<button type="submit"><?php esc_html_e( 'Gửi lời hẹn đến Mộc', 'moc-studio' ); ?> &nbsp;→</button>
				<p class="ok" id="ok" role="status"><?php esc_html_e( 'Cảm ơn bạn! Mộc sẽ liên hệ với bạn sớm nhất. Cần gấp?', 'moc-studio' ); ?> <a data-zalo href="#" style="text-decoration:underline"><?php esc_html_e( 'Nhắn Mộc qua Zalo', 'moc-studio' ); ?></a>.</p>
				<p class="note2"><?php esc_html_e( 'Thông tin của bạn chỉ dùng để tư vấn buổi chụp.', 'moc-studio' ); ?></p>
			</form>
		</div>
	</section>
</main>

<?php
get_footer();
