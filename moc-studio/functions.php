<?php
/**
 * MỘC Studio — functions.php
 *
 * File này chạy mỗi lần WordPress tải theme. Nó KHÔNG in ra HTML,
 * chỉ "móc" (hook) code của theme vào các thời điểm WordPress cho phép:
 *   - after_setup_theme   → khai báo tính năng theme hỗ trợ
 *   - wp_enqueue_scripts  → nạp CSS/JS ngoài trang web
 *   - wp_head             → chèn thêm thẻ vào <head>
 *
 * @package moc-studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Chặn truy cập trực tiếp vào file.
}

define( 'MOC_STUDIO_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'MOC_STUDIO_DIR', get_template_directory() );
define( 'MOC_STUDIO_URI', get_template_directory_uri() );

// Tách phần lớn ra file riêng cho dễ đọc.
require MOC_STUDIO_DIR . '/inc/customizer.php'; // Bảng giá + link liên hệ.
require MOC_STUDIO_DIR . '/inc/album-cpt.php';  // Custom post type "Bộ ảnh".

/* -------------------------------------------------------------------------
 * 1. Khai báo tính năng theme
 * ---------------------------------------------------------------------- */
function moc_studio_setup() {
	load_theme_textdomain( 'moc-studio', MOC_STUDIO_DIR . '/languages' );

	add_theme_support( 'title-tag' );          // WP tự in <title>, không hard-code trong header.php.
	add_theme_support( 'post-thumbnails' );    // Ảnh đại diện — dùng làm ảnh bìa bộ ảnh.
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'script', 'style', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Menu chính', 'moc-studio' ),
		)
	);
}
add_action( 'after_setup_theme', 'moc_studio_setup' );

/* -------------------------------------------------------------------------
 * 2. Nạp CSS / JS đúng chuẩn
 *    Không viết <link>/<script> tay trong header.php vì:
 *    - WP & plugin biết file nào đã nạp → không trùng (vd. jQuery chỉ 1 lần).
 *    - Khai báo phụ thuộc (deps) → đúng thứ tự.
 *    - Có version → trình duyệt tải lại khi file đổi (cache busting).
 * ---------------------------------------------------------------------- */
function moc_studio_asset_version( $relative_path ) {
	$file = MOC_STUDIO_DIR . '/' . $relative_path;
	// Lúc dev dùng thời gian sửa file để luôn có bản mới; nếu không thì dùng version theme.
	return file_exists( $file ) ? (string) filemtime( $file ) : MOC_STUDIO_VERSION;
}

function moc_studio_enqueue_assets() {
	// Google Fonts — version null để không gắn ?ver= vào URL của Google.
	wp_enqueue_style(
		'moc-studio-fonts',
		'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Inter:wght@500;600&family=Playfair+Display:wght@500&display=swap&subset=vietnamese',
		array(),
		null
	);

	// style.css của theme (vừa là header theme, vừa chứa toàn bộ CSS).
	wp_enqueue_style(
		'moc-studio-style',
		get_stylesheet_uri(),
		array( 'moc-studio-fonts' ),
		moc_studio_asset_version( 'style.css' )
	);

	// JS thuần: lightbox, form, hiệu ứng hiện dần khi cuộn.
	wp_enqueue_script(
		'moc-studio-main',
		MOC_STUDIO_URI . '/assets/js/main.js',
		array(),
		moc_studio_asset_version( 'assets/js/main.js' ),
		array( 'in_footer' => true )
	);

	// Hiệu ứng menu khi cuộn — viết bằng jQuery, nên khai báo 'jquery' là phụ thuộc.
	// WP dùng bản jQuery có sẵn trong core, không cần tải từ CDN.
	wp_enqueue_script(
		'moc-studio-scroll',
		MOC_STUDIO_URI . '/assets/js/scroll-effects.js',
		array( 'jquery' ),
		moc_studio_asset_version( 'assets/js/scroll-effects.js' ),
		array( 'in_footer' => true )
	);

	// Truyền dữ liệu từ PHP (Customizer + CPT) sang JS dưới dạng window.MOC_DATA,
	// in ngay TRƯỚC main.js. wp_json_encode tự escape an toàn cho <script>.
	$data = array(
		'contact' => moc_studio_get_contact_links(),
		'albums'  => moc_studio_get_albums_for_js(),
	);
	wp_add_inline_script(
		'moc-studio-main',
		'window.MOC_DATA = ' . wp_json_encode( $data ) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'moc_studio_enqueue_assets' );

/* -------------------------------------------------------------------------
 * 3. Thêm vài thẻ vào <head>
 * ---------------------------------------------------------------------- */

// Thêm class "js" vào <html> càng sớm càng tốt, để CSS ẩn nội dung chờ hiệu ứng
// chỉ khi JS chạy được (không có JS thì nội dung vẫn hiện bình thường).
function moc_studio_js_class() {
	wp_print_inline_script_tag( 'document.documentElement.className+=" js";' );
}
add_action( 'wp_head', 'moc_studio_js_class', 0 );

function moc_studio_head_meta() {
	$desc = get_bloginfo( 'description' );
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	echo '<meta name="theme-color" content="#302820">' . "\n";

	// Nếu admin chưa đặt "Biểu tượng trang" (Site Icon) trong Customizer thì dùng favicon của theme.
	if ( ! has_site_icon() ) {
		$fav = MOC_STUDIO_URI . '/assets/images/favicon/';
		printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( $fav . 'favicon.ico' ) );
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( $fav . 'favicon.svg' ) );
		printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $fav . 'apple-touch-icon.png' ) );
	}
}
add_action( 'wp_head', 'moc_studio_head_meta', 2 );

// Preconnect tới Google Fonts — dùng filter chuẩn của WP thay vì in tay.
function moc_studio_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'moc_studio_resource_hints', 10, 2 );

/* -------------------------------------------------------------------------
 * 4. Hàm tiện ích dùng trong template
 * ---------------------------------------------------------------------- */

/** URL ảnh nằm trong theme: assets/images/<file>. */
function moc_studio_img( $file ) {
	return MOC_STUDIO_URI . '/assets/images/' . ltrim( $file, '/' );
}

/** Tên trang viết hoa (logo, footer). mb_* để viết hoa đúng chữ có dấu: "Mộc" → "MỘC". */
function moc_studio_site_name_upper() {
	$name = get_bloginfo( 'name' );
	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $name, 'UTF-8' ) : $name;
}

/** Link tới một mục trên trang chủ, dùng được cả khi đang ở trang khác. */
function moc_studio_anchor( $hash ) {
	return is_front_page() ? '#' . $hash : home_url( '/#' . $hash );
}

/** Menu mặc định khi admin chưa tạo menu trong Giao diện → Menu. */
function moc_studio_default_menu() {
	$items = array(
		'top'     => __( 'Giới thiệu', 'moc-studio' ),
		'bo-anh'  => __( 'Bộ ảnh', 'moc-studio' ),
		'dich-vu' => __( 'Dịch vụ', 'moc-studio' ),
		'moc'     => __( 'Về Mộc', 'moc-studio' ),
	);
	echo '<ul>';
	foreach ( $items as $hash => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( moc_studio_anchor( $hash ) ), esc_html( $label ) );
	}
	echo '</ul>';
}
