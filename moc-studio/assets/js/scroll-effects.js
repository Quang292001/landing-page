/**
 * Hiệu ứng khi cuộn — viết lại bằng jQuery (bản gốc là JS thuần trong main.js).
 *   1. Thanh tiến trình đọc ở đầu trang
 *   2. Menu đổi nền khi rời đầu trang, tự ẩn khi kéo xuống, hiện lại khi kéo lên
 *   3. Nút Zalo / TikTok nổi hiện ra sau 400px
 *   4. Scrollspy: gạch chân mục menu ứng với section đang xem
 *   5. Parallax nhẹ cho ảnh lớn
 *
 * WordPress nạp jQuery ở chế độ noConflict → biến $ KHÔNG có sẵn.
 * jQuery(function ($) { ... }) vừa đợi DOM sẵn sàng, vừa nhận $ làm tham số an toàn.
 */
jQuery(function ($) {
  'use strict';

  var $win = $(window);
  var $nav = $('.nav');
  var $bar = $('.progress');
  var $float = $('.float');
  var $parallax = $('.hero .ph img, .about .ph img');
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Scrollspy: so theo this.hash nên chạy được cả với link "#bo-anh"
  // lẫn "https://site.com/#bo-anh" do menu WordPress sinh ra.
  var spy = [
    ['#top', '.hero'], ['#bo-anh', '#bo-anh'], ['#moc', '#moc'], ['#dich-vu', '#dich-vu']
  ].map(function (p) {
    return {
      $link: $nav.find('ul a').filter(function () { return this.hash === p[0]; }),
      $el: $(p[1])
    };
  }).filter(function (s) {
    return s.$link.length && s.$el.length;
  });

  var lastY = $win.scrollTop();
  var ticking = false;

  function update() {
    ticking = false;
    var y = $win.scrollTop();
    var vh = $win.height();
    var max = $(document).height() - vh;

    // 1. Thanh tiến trình — scaleX thay vì width để GPU xử lý, không gây reflow.
    $bar.css('transform', 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')');

    // 2. Menu
    $nav.toggleClass('is-scrolled', y > 10);
    var delta = y - lastY;
    if (Math.abs(delta) > 6) {            // Bỏ qua rung cuộn nhỏ.
      $nav.toggleClass('is-hidden', delta > 0 && y > 200);
      lastY = y;
    }
    if (y < 200) $nav.removeClass('is-hidden');

    // 3. Nút nổi
    $float.toggleClass('show', y > 400);

    // 4. Scrollspy: section cuối cùng có mép trên đã qua 40% màn hình là section đang xem.
    if (spy.length) {
      var current = spy[0];
      $.each(spy, function (_, s) {
        if (s.$el[0].getBoundingClientRect().top <= vh * 0.4) current = s;
      });
      $.each(spy, function (_, s) {
        s.$link.toggleClass('active', s === current);
      });
    }

    // 5. Parallax: ảnh lệch tối đa ±22px theo khoảng cách tới giữa màn hình.
    if (!reduce) {
      $parallax.each(function () {
        var r = this.parentNode.getBoundingClientRect();
        var offset = (r.top + r.height / 2 - vh / 2) * -0.06;
        this.style.setProperty('--py', Math.max(-22, Math.min(22, offset)).toFixed(1) + 'px');
      });
    }
  }

  // Sự kiện scroll bắn rất dày → gom lại, mỗi khung hình chỉ cập nhật 1 lần.
  function onScroll() {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(update);
    }
  }

  // Namespace ".moc" để sau này gỡ riêng handler của theme: $win.off('.moc').
  $win.on('scroll.moc resize.moc', onScroll);
  update();
});
