<?php
/**
 * Customizer: Giao diện → Tùy biến → "MỘC Studio".
 *
 * Dùng Customizer cho dữ liệu ÍT và CỐ ĐỊNH (3 gói giá, vài link liên hệ):
 * admin sửa và xem trước ngay trên trang, giá trị lưu bằng theme_mod.
 * Dữ liệu NHIỀU và thêm/bớt tùy ý (bộ ảnh) thì dùng custom post type — xem album-cpt.php.
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Giá trị mặc định = nội dung landing page gốc, để theme vừa kích hoạt đã có nội dung. */
function moc_studio_package_defaults() {
	return array(
		1 => array(
			'label'    => '01 / CHÂN DUNG',
			'title'    => 'Là chính mình',
			'price'    => 'Từ 1.500.000đ',
			'desc'     => 'Một khoảng riêng, một phiên bản thật nhất.',
			'features' => "60 phút chụp tại studio hoặc ngoại cảnh\n01 trang phục · 15 ảnh chỉnh sửa · Tư vấn concept",
			'service'  => 'Chân dung',
		),
		2 => array(
			'label'    => '02 / SINH NHẬT',
			'title'    => 'Chúc mừng sinh nhật',
			'price'    => 'Từ 2.800.000đ',
			'desc'     => 'Những khoảnh khắc đáng nhớ trong ngày sinh nhật.',
			'features' => "90 phút chụp · 02 trang phục\n25 ảnh chỉnh sửa · Tư vấn concept",
			'service'  => 'Sinh nhật',
		),
		3 => array(
			'label'    => '03 / NÀNG THƠ',
			'title'    => 'Nhẹ nhàng bay bổng',
			'price'    => 'Từ 3.200.000đ',
			'desc'     => 'Một không gian nhẹ nhàng, đầy cảm xúc.',
			'features' => "90 phút chụp và quay video hậu trường + makeup\n30 ảnh chỉnh sửa · 01 ảnh in 20 × 30 · Tư vấn concept",
			'service'  => 'Nàng thơ',
		),
	);
}

function moc_studio_contact_defaults() {
	return array(
		'zalo'      => 'https://zalo.me/0900000000',
		'tiktok'    => 'https://www.tiktok.com/@mocstudio',
		'gallery'   => 'https://www.tiktok.com/@mocstudio',
		'instagram' => '',
		'facebook'  => '',
		'behance'   => '',
	);
}

/** Sanitize textarea: mỗi dòng là một ý, bỏ thẻ HTML. */
function moc_studio_sanitize_lines( $value ) {
	return sanitize_textarea_field( $value );
}

function moc_studio_customize_register( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_panel(
		'moc_studio',
		array(
			'title'    => __( 'MỘC Studio', 'moc-studio' ),
			'priority' => 30,
		)
	);

	/* ---------- Bảng giá: 3 section, mỗi gói 1 section ---------- */
	$fields = array(
		'label'    => array( __( 'Nhãn (vd. 01 / CHÂN DUNG)', 'moc-studio' ), 'text', 'sanitize_text_field' ),
		'title'    => array( __( 'Tên gói', 'moc-studio' ), 'text', 'sanitize_text_field' ),
		'price'    => array( __( 'Giá', 'moc-studio' ), 'text', 'sanitize_text_field' ),
		'desc'     => array( __( 'Mô tả ngắn', 'moc-studio' ), 'text', 'sanitize_text_field' ),
		'features' => array( __( 'Quyền lợi (mỗi dòng một ý)', 'moc-studio' ), 'textarea', 'moc_studio_sanitize_lines' ),
		'service'  => array( __( 'Tên loại chụp trong form đặt lịch', 'moc-studio' ), 'text', 'sanitize_text_field' ),
	);

	foreach ( moc_studio_package_defaults() as $i => $defaults ) {
		$section = 'moc_pkg_' . $i;
		$wp_customize->add_section(
			$section,
			array(
				/* translators: %d: số thứ tự gói chụp. */
				'title' => sprintf( __( 'Bảng giá — Gói %d', 'moc-studio' ), $i ),
				'panel' => 'moc_studio',
			)
		);

		foreach ( $fields as $key => $field ) {
			list( $label, $type, $sanitize ) = $field;
			$id = "moc_pkg_{$i}_{$key}";

			$wp_customize->add_setting(
				$id,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => $sanitize, // Luôn làm sạch dữ liệu trước khi lưu DB.
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$id,
				array(
					'label'   => $label,
					'section' => $section,
					'type'    => $type,
				)
			);
		}
	}

	/* ---------- Link liên hệ ---------- */
	$wp_customize->add_section(
		'moc_contact',
		array(
			'title' => __( 'Liên hệ & mạng xã hội', 'moc-studio' ),
			'panel' => 'moc_studio',
		)
	);

	$labels = array(
		'zalo'      => __( 'Link Zalo', 'moc-studio' ),
		'tiktok'    => __( 'Link TikTok', 'moc-studio' ),
		'gallery'   => __( 'Link "Xem tất cả bộ ảnh"', 'moc-studio' ),
		'instagram' => __( 'Link Instagram', 'moc-studio' ),
		'facebook'  => __( 'Link Facebook', 'moc-studio' ),
		'behance'   => __( 'Link Behance', 'moc-studio' ),
	);
	foreach ( moc_studio_contact_defaults() as $key => $default ) {
		$id = 'moc_link_' . $key;
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $labels[ $key ],
				'section' => 'moc_contact',
				'type'    => 'url',
			)
		);
	}
}
add_action( 'customize_register', 'moc_studio_customize_register' );

/* -------------------------------------------------------------------------
 * Hàm đọc dữ liệu cho template — template không gọi get_theme_mod() rải rác,
 * chỉ gọi 2 hàm này.
 * ---------------------------------------------------------------------- */

/** @return array[] Danh sách gói chụp, 'features' đã tách thành mảng dòng. */
function moc_studio_get_packages() {
	$packages = array();
	foreach ( moc_studio_package_defaults() as $i => $defaults ) {
		$pkg = array();
		foreach ( $defaults as $key => $default ) {
			$pkg[ $key ] = get_theme_mod( "moc_pkg_{$i}_{$key}", $default );
		}
		$pkg['features'] = array_values( array_filter( array_map( 'trim', explode( "\n", $pkg['features'] ) ) ) );

		if ( '' !== $pkg['title'] ) { // Xóa tên gói = ẩn gói đó.
			$packages[] = $pkg;
		}
	}
	return $packages;
}

/** @return array<string,string> Link liên hệ, key: zalo, tiktok, gallery, instagram, facebook, behance. */
function moc_studio_get_contact_links() {
	$links = array();
	foreach ( moc_studio_contact_defaults() as $key => $default ) {
		$links[ $key ] = get_theme_mod( 'moc_link_' . $key, $default );
	}
	return $links;
}
