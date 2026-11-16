<?php
/**
 * Dữ liệu mẫu cho bản demo trên WordPress Playground.
 * Tạo 3 "Bộ ảnh" (custom post type moc_album) từ ảnh có sẵn trong theme,
 * để người xem vào admin thấy ngay menu "Bộ ảnh" có nội dung.
 *
 * Không chạy trực tiếp: build-blueprint.js nhúng file này vào bước runPHP của blueprint.json.
 */
require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$dir    = get_template_directory() . '/assets/images/';
$albums = array(
	array( 'Một ngày, là chính mình', 'chan-dung', 'Chân dung / Ngoại cảnh', 'Chân dung', array( 'Natural-Light-AdobeStock.jpeg', 'chandung02.avif', 'chandung03.png' ) ),
	array( 'Chúc mừng sinh nhật', 'sinh-nhat', 'Sinh nhật / Studio', 'Sinh nhật', array( 'sinhnhat.jpg', 'sinhnhat02.jpg', 'sinhnhat03.jpg' ) ),
	array( 'Nàng thơ nhẹ nhàng', 'nang-tho', 'Nàng thơ / Studio', 'Nàng thơ', array( 'nangtho.jpg' ) ),
);

foreach ( $albums as $i => $a ) {
	list( $title, $slug, $meta, $service, $files ) = $a;

	$ids = array();
	foreach ( $files as $file ) {
		$upload = wp_upload_bits( $file, null, file_get_contents( $dir . $file ) );
		if ( ! empty( $upload['error'] ) ) {
			continue;
		}
		$type = wp_check_filetype( $upload['file'] );
		$id   = wp_insert_attachment(
			array(
				'post_mime_type' => $type['type'],
				'post_title'     => $title,
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		$ids[] = $id;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'moc_album',
			'post_status' => 'publish',
			'post_title'  => $title,
			'post_name'   => $slug,
			'menu_order'  => $i,
		)
	);
	update_post_meta( $post_id, '_moc_album_meta', $meta );
	update_post_meta( $post_id, '_moc_album_service', $service );
	update_post_meta( $post_id, '_moc_album_photos', implode( ',', $ids ) );
	if ( $ids ) {
		set_post_thumbnail( $post_id, $ids[0] );
	}
}
