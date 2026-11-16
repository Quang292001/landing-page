<?php
/**
 * Custom post type "Bộ ảnh" (moc_album).
 *
 * Mỗi bộ ảnh là một bài viết riêng trong menu "Bộ ảnh" ở trang quản trị:
 *   - Tiêu đề         → tên bộ ảnh
 *   - Ảnh đại diện    → ảnh bìa trên card (bỏ trống thì lấy ảnh đầu tiên)
 *   - Thứ tự          → thứ tự hiển thị (Thuộc tính trang → Thứ tự)
 *   - Meta box        → nhãn phụ, loại chụp, danh sách ảnh chọn từ Thư viện Media
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Đăng ký post type
 * ---------------------------------------------------------------------- */
function moc_studio_register_album_cpt() {
	register_post_type(
		'moc_album',
		array(
			'labels'          => array(
				'name'          => __( 'Bộ ảnh', 'moc-studio' ),
				'singular_name' => __( 'Bộ ảnh', 'moc-studio' ),
				'add_new'       => __( 'Thêm bộ ảnh', 'moc-studio' ),
				'add_new_item'  => __( 'Thêm bộ ảnh mới', 'moc-studio' ),
				'edit_item'     => __( 'Sửa bộ ảnh', 'moc-studio' ),
				'all_items'     => __( 'Tất cả bộ ảnh', 'moc-studio' ),
			),
			'public'          => false, // Không có trang riêng /moc_album/..., chỉ hiện trên trang chủ.
			'show_ui'         => true,  // Nhưng vẫn có màn hình quản lý trong admin.
			'show_in_menu'    => true,
			'show_in_rest'    => false, // Dùng màn hình sửa cổ điển cho meta box gọn.
			'menu_position'   => 20,
			'menu_icon'       => 'dashicons-format-gallery',
			'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'moc_studio_register_album_cpt' );

/* -------------------------------------------------------------------------
 * 2. Meta box: nhãn phụ, loại chụp, ảnh trong bộ
 * ---------------------------------------------------------------------- */
function moc_studio_album_add_meta_box() {
	add_meta_box(
		'moc_album_details',
		__( 'Chi tiết bộ ảnh', 'moc-studio' ),
		'moc_studio_album_meta_box_html',
		'moc_album',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'moc_studio_album_add_meta_box' );

function moc_studio_album_meta_box_html( WP_Post $post ) {
	$meta    = get_post_meta( $post->ID, '_moc_album_meta', true );
	$service = get_post_meta( $post->ID, '_moc_album_service', true );
	$ids     = moc_studio_album_photo_ids( $post->ID );

	// Nonce: chứng minh form gửi lên đúng từ màn hình này (chống CSRF).
	wp_nonce_field( 'moc_album_save', 'moc_album_nonce' );
	?>
	<p>
		<label for="moc_album_meta"><strong><?php esc_html_e( 'Nhãn phụ', 'moc-studio' ); ?></strong></label><br>
		<input type="text" id="moc_album_meta" name="moc_album_meta" class="widefat" value="<?php echo esc_attr( $meta ); ?>" placeholder="Chân dung / Ngoại cảnh">
	</p>
	<p>
		<label for="moc_album_service"><strong><?php esc_html_e( 'Loại chụp', 'moc-studio' ); ?></strong></label><br>
		<input type="text" id="moc_album_service" name="moc_album_service" class="widefat" value="<?php echo esc_attr( $service ); ?>" placeholder="Chân dung">
		<span class="description"><?php esc_html_e( 'Trùng với "Tên loại chụp" của một gói trong Customizer để nút "Đặt buổi chụp như thế này" chọn sẵn đúng gói.', 'moc-studio' ); ?></span>
	</p>

	<p><strong><?php esc_html_e( 'Ảnh trong bộ', 'moc-studio' ); ?></strong>
		<span class="description"><?php esc_html_e( '— kéo thả để đổi thứ tự.', 'moc-studio' ); ?></span></p>
	<ul class="moc-album-photos">
		<?php foreach ( $ids as $id ) : ?>
			<li data-id="<?php echo esc_attr( $id ); ?>">
				<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
				<button type="button" class="moc-album-remove" aria-label="<?php esc_attr_e( 'Bỏ ảnh', 'moc-studio' ); ?>">&times;</button>
			</li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="moc_album_photos" class="moc-album-photos-input" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
	<p><button type="button" class="button moc-album-add"><?php esc_html_e( 'Thêm ảnh từ Thư viện', 'moc-studio' ); ?></button></p>
	<?php
}

function moc_studio_album_save( $post_id ) {
	// 4 lớp kiểm tra trước khi lưu meta:
	if ( ! isset( $_POST['moc_album_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['moc_album_nonce'] ) ), 'moc_album_save' ) ) {
		return; // 1. Nonce sai → không phải từ form của mình.
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return; // 2. Bỏ qua autosave.
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return; // 3. Bỏ qua bản revision.
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return; // 4. Người dùng không có quyền sửa bài này.
	}

	if ( isset( $_POST['moc_album_meta'] ) ) {
		update_post_meta( $post_id, '_moc_album_meta', sanitize_text_field( wp_unslash( $_POST['moc_album_meta'] ) ) );
	}
	if ( isset( $_POST['moc_album_service'] ) ) {
		update_post_meta( $post_id, '_moc_album_service', sanitize_text_field( wp_unslash( $_POST['moc_album_service'] ) ) );
	}
	if ( isset( $_POST['moc_album_photos'] ) ) {
		// Chỉ giữ số nguyên dương — chuỗi "12,5,abc" → array(12, 5).
		$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['moc_album_photos'] ) ) ) ) );
		update_post_meta( $post_id, '_moc_album_photos', implode( ',', $ids ) );
	}
}
add_action( 'save_post_moc_album', 'moc_studio_album_save' );

/* -------------------------------------------------------------------------
 * 3. JS/CSS cho meta box — chỉ nạp ở màn hình sửa bộ ảnh
 * ---------------------------------------------------------------------- */
function moc_studio_album_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'moc_album' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media(); // Nạp hộp thoại Thư viện Media (wp.media).
	wp_enqueue_script(
		'moc-studio-album-admin',
		MOC_STUDIO_URI . '/assets/js/admin-album.js',
		array( 'jquery', 'jquery-ui-sortable' ), // Cả hai có sẵn trong core WP.
		moc_studio_asset_version( 'assets/js/admin-album.js' ),
		array( 'in_footer' => true )
	);
	wp_localize_script(
		'moc-studio-album-admin',
		'MOC_ALBUM_ADMIN',
		array(
			'title'  => __( 'Chọn ảnh cho bộ ảnh', 'moc-studio' ),
			'button' => __( 'Thêm vào bộ ảnh', 'moc-studio' ),
			'remove' => __( 'Bỏ ảnh', 'moc-studio' ),
		)
	);
	wp_add_inline_style(
		'common',
		'.moc-album-photos{display:flex;flex-wrap:wrap;gap:8px;margin:0}
		.moc-album-photos li{position:relative;margin:0;cursor:move}
		.moc-album-photos img{display:block;width:96px;height:96px;object-fit:cover}
		.moc-album-remove{position:absolute;top:2px;right:2px;border:0;border-radius:50%;width:22px;height:22px;line-height:20px;background:#000a;color:#fff;cursor:pointer}'
	);
}
add_action( 'admin_enqueue_scripts', 'moc_studio_album_admin_assets' );

/* -------------------------------------------------------------------------
 * 4. Lấy dữ liệu cho front-end
 * ---------------------------------------------------------------------- */

/** @return int[] ID ảnh của bộ ảnh, đúng thứ tự admin sắp xếp. */
function moc_studio_album_photo_ids( $post_id ) {
	$raw = (string) get_post_meta( $post_id, '_moc_album_photos', true );
	return array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
}

/** Bộ ảnh mẫu dùng khi admin chưa tạo bộ ảnh nào — trang vẫn đầy đủ như bản tĩnh. */
function moc_studio_default_albums() {
	$demo = array(
		'chan-dung' => array( 'Một ngày, là chính mình', 'Chân dung / Ngoại cảnh', 'Chân dung', array( 'Natural-Light-AdobeStock.jpeg', 'chandung02.avif', 'chandung03.png' ) ),
		'sinh-nhat' => array( 'Chúc mừng sinh nhật', 'Sinh nhật / Studio', 'Sinh nhật', array( 'sinhnhat.jpg', 'sinhnhat02.jpg', 'sinhnhat03.jpg' ) ),
		'nang-tho'  => array( 'Nàng thơ nhẹ nhàng', 'Nàng thơ / Studio', 'Nàng thơ', array( 'nangtho.jpg' ) ),
	);

	$albums = array();
	foreach ( $demo as $key => $a ) {
		$photos   = array_map( 'moc_studio_img', $a[3] );
		$albums[] = array(
			'key'     => $key,
			'title'   => $a[0],
			'meta'    => $a[1],
			'service' => $a[2],
			'cover'   => sprintf( '<img src="%s" alt="%s" loading="lazy">', esc_url( $photos[0] ), esc_attr( $a[0] ) ),
			'photos'  => $photos,
			'thumbs'  => $photos,
		);
	}
	return $albums;
}

/**
 * Danh sách bộ ảnh đã chuẩn hóa — template và JS dùng chung một nguồn.
 * Kết quả được nhớ trong biến static để chỉ query DB một lần mỗi request.
 *
 * @return array[] key, title, meta, service, cover (HTML <img>), photos (URL), thumbs (URL).
 */
function moc_studio_get_albums() {
	static $albums = null;
	if ( null !== $albums ) {
		return $albums;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'moc_album',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => true, // Không cần phân trang → bỏ câu COUNT(*).
		)
	);

	$albums = array();
	foreach ( $query->posts as $post ) {
		$ids = moc_studio_album_photo_ids( $post->ID );
		if ( has_post_thumbnail( $post ) ) {
			$cover_id = get_post_thumbnail_id( $post );
		} elseif ( $ids ) {
			$cover_id = $ids[0];
		} else {
			continue; // Bộ ảnh chưa có ảnh nào → bỏ qua.
		}
		if ( ! $ids ) {
			$ids = array( $cover_id );
		}

		$title    = get_the_title( $post );
		$albums[] = array(
			'key'     => $post->post_name ? $post->post_name : 'album-' . $post->ID,
			'title'   => $title,
			'meta'    => get_post_meta( $post->ID, '_moc_album_meta', true ),
			'service' => get_post_meta( $post->ID, '_moc_album_service', true ),
			// wp_get_attachment_image tự sinh srcset/sizes → trình duyệt chọn ảnh vừa màn hình.
			'cover'   => wp_get_attachment_image(
				$cover_id,
				'large',
				false,
				array(
					'loading' => 'lazy',
					'alt'     => $title,
				)
			),
			'photos'  => array_values( array_filter( array_map( static function ( $id ) {
				return wp_get_attachment_image_url( $id, 'large' );
			}, $ids ) ) ),
			'thumbs'  => array_values( array_filter( array_map( static function ( $id ) {
				return wp_get_attachment_image_url( $id, 'thumbnail' );
			}, $ids ) ) ),
		);
	}

	if ( ! $albums ) {
		$albums = moc_studio_default_albums();
	}
	return $albums;
}

/** Dạng gọn cho JS: { "chan-dung": { title, meta, service, photos, thumbs }, ... } */
function moc_studio_get_albums_for_js() {
	$out = array();
	foreach ( moc_studio_get_albums() as $a ) {
		$out[ $a['key'] ] = array(
			'title'   => $a['title'],
			'meta'    => $a['meta'],
			'service' => $a['service'],
			'photos'  => $a['photos'],
			'thumbs'  => $a['thumbs'],
		);
	}
	return $out;
}
