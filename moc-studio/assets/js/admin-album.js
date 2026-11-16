/**
 * Meta box "Chi tiết bộ ảnh": chọn ảnh từ Thư viện Media, kéo thả sắp xếp, bỏ ảnh.
 * Danh sách ID ảnh được ghi vào input ẩn "moc_album_photos" để PHP lưu khi bấm Cập nhật.
 */
jQuery(function ($) {
  'use strict';

  var $list = $('.moc-album-photos');
  var $input = $('.moc-album-photos-input');
  var frame = null;

  if (!$list.length) return;

  // Đọc lại thứ tự <li> rồi ghi vào input ẩn.
  function sync() {
    var ids = $list.children('li').map(function () {
      return $(this).data('id');
    }).get();
    $input.val(ids.join(','));
  }

  function addItem(id, url) {
    if ($list.children('[data-id="' + id + '"]').length) return; // Không thêm trùng.
    $('<li>', { 'data-id': id })
      .append($('<img>', { src: url, alt: '' }))
      .append($('<button>', {
        type: 'button',
        'class': 'moc-album-remove',
        'aria-label': MOC_ALBUM_ADMIN.remove,
        html: '&times;'
      }))
      .appendTo($list);
  }

  // Kéo thả đổi thứ tự (jQuery UI Sortable có sẵn trong WordPress).
  $list.sortable({ update: sync });

  // Bỏ một ảnh — dùng event delegation vì <li> có thể được thêm sau.
  $list.on('click', '.moc-album-remove', function () {
    $(this).closest('li').remove();
    sync();
  });

  // Mở Thư viện Media, cho chọn nhiều ảnh.
  $('.moc-album-add').on('click', function (e) {
    e.preventDefault();
    if (!frame) {
      frame = wp.media({
        title: MOC_ALBUM_ADMIN.title,
        button: { text: MOC_ALBUM_ADMIN.button },
        library: { type: 'image' },
        multiple: 'add'
      });
      frame.on('select', function () {
        frame.state().get('selection').each(function (att) {
          var a = att.toJSON();
          var url = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
          addItem(a.id, url);
        });
        sync();
      });
    }
    frame.open();
  });
});
