/* ===== Liên hệ — sửa 2 dòng này cho đúng tài khoản của Mộc ===== */
var CONTACT = {
  zalo:    'https://zalo.me/0900000000',        // thay bằng số điện thoại Zalo
  tiktok:  'https://www.tiktok.com/@mocstudio', // thay bằng tài khoản TikTok
  gallery: 'https://www.tiktok.com/@mocstudio'  // link "Xem tất cả bộ ảnh" (TikTok / Instagram / Facebook)
};

/* ===== Bộ ảnh hiển thị trong popup =====
   - Khoá ('chan-dung'...) phải trùng data-album của card trong index.html.
   - service: tên lựa chọn trong ô "Bạn muốn chụp gì?" — tự chọn sẵn khi bấm "Đặt buổi chụp như thế này".
   - photos: ảnh đầu tiên nên là ảnh bìa. Thêm ảnh vào cuối danh sách.
     Gợi ý: để ảnh trong assets/albums/<tên-bộ>/ và nén cạnh dài ~1600px. */
var ALBUMS = {
  'chan-dung': {
    title: 'Một ngày, là chính mình',
    meta: 'Chân dung / Ngoại cảnh',
    service: 'Chân dung',
    photos: [
      'assets/Natural-Light-AdobeStock.jpeg',
      'assets/chandung02.avif',
       'assets/chandung03.png',
    ]
  },
  'sinh-nhat': {
    title: 'Chúc mừng sinh nhật',
    meta: 'Sinh nhật / Studio',
    service: 'Sinh nhật',
    photos: [
      'assets/sinhnhat.jpg',
      'assets/sinhnhat02.jpg',
      'assets/sinhnhat03.jpg'
    ]
  },
  'nang-tho': {
    title: 'Nàng thơ tím',
    meta: 'Nàng thơ / Studio',
    service: 'Nàng thơ',
    photos: [
      'assets/nangtho.jpg'
      // 'assets/albums/nang-tho/02.jpg',
    ]
  }
};

(function () {
  'use strict';

  /* ---------- Gắn link Zalo / TikTok cho mọi nút có data-zalo / data-tiktok ---------- */
  function bindLinks(attr, url) {
    document.querySelectorAll('[' + attr + ']').forEach(function (a) {
      a.href = url;
      a.target = '_blank';
      a.rel = 'noopener noreferrer';
    });
  }
  bindLinks('data-zalo', CONTACT.zalo);
  bindLinks('data-tiktok', CONTACT.tiktok);
  bindLinks('data-gallery', CONTACT.gallery);

  /* ---------- Form đặt lịch ---------- */
  var form = document.getElementById('booking-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var ok = true;
      form.querySelectorAll('[required]').forEach(function (el) {
        var bad = !el.value.trim();
        el.style.borderBottomColor = bad ? '#e0a38f' : '';
        if (bad) ok = false;
      });
      if (!ok) return;
      // TODO: gửi dữ liệu tới backend / Google Form / Formspree tại đây
      document.getElementById('ok').style.display = 'block';
      form.reset();
    });
  }

  /* ---------- Lightbox: xem bộ ảnh trong popup ---------- */
  var lb = document.getElementById('lightbox');

  if (lb && typeof lb.showModal === 'function') {
    var lbImg = lb.querySelector('.lb-img');
    var lbTitle = lb.querySelector('.lb-title');
    var lbMeta = lb.querySelector('.lb-meta');
    var lbCount = lb.querySelector('.lb-count');
    var lbThumbs = lb.querySelector('.lb-thumbs');
    var lbPrev = lb.querySelector('.lb-prev');
    var lbNext = lb.querySelector('.lb-next');
    var lbBook = lb.querySelector('.lb-book');
    var album = null, index = 0, opener = null;

    // Nhãn "Xem bộ ảnh · N ảnh" trên từng card
    document.querySelectorAll('.card[data-album]').forEach(function (card) {
      var a = ALBUMS[card.dataset.album];
      var badge = card.querySelector('.card-count');
      if (a && badge) badge.textContent = 'Xem bộ ảnh · ' + a.photos.length + ' ảnh';
    });

    function preload(i) {
      var src = album.photos[(i + album.photos.length) % album.photos.length];
      if (src) new Image().src = src;
    }

    function show(i) {
      var n = album.photos.length;
      index = (i + n) % n;
      lbImg.src = album.photos[index];
      lbImg.alt = album.title + ' — ảnh ' + (index + 1) + '/' + n;
      lbCount.textContent = (index + 1) + ' / ' + n;

      // chạy lại hiệu ứng đổi ảnh
      lbImg.classList.remove('is-changing');
      void lbImg.offsetWidth;
      lbImg.classList.add('is-changing');

      [].forEach.call(lbThumbs.children, function (t, k) {
        t.setAttribute('aria-current', k === index ? 'true' : 'false');
        if (k === index) t.scrollIntoView({ block: 'nearest', inline: 'nearest' });
      });

      if (n > 1) { preload(index + 1); preload(index - 1); }
    }

    function open(key, btn) {
      album = ALBUMS[key];
      if (!album || !album.photos.length) return;
      opener = btn;

      lbTitle.textContent = album.title;
      lbMeta.textContent = album.meta;

      var multi = album.photos.length > 1;
      lbPrev.hidden = lbNext.hidden = lbThumbs.hidden = !multi;
      lbThumbs.innerHTML = '';
      if (multi) {
        album.photos.forEach(function (src, k) {
          var t = document.createElement('button');
          t.type = 'button';
          t.className = 'lb-thumb';
          t.setAttribute('aria-label', 'Ảnh ' + (k + 1));
          t.innerHTML = '<img src="' + src + '" alt="" loading="lazy">';
          t.addEventListener('click', function () { show(k); });
          lbThumbs.appendChild(t);
        });
      }

      document.documentElement.style.overflow = 'hidden';
      lb.showModal();
      show(0);
    }

    function close() {
      if (lb.open) lb.close();
    }

    lb.addEventListener('close', function () {
      document.documentElement.style.overflow = '';
      if (opener) opener.focus({ preventScroll: true });
    });

    document.querySelectorAll('.card[data-album] .card-hit').forEach(function (btn) {
      btn.addEventListener('click', function () {
        open(btn.closest('.card').dataset.album, btn);
      });
    });

    lb.querySelector('.lb-close').addEventListener('click', close);
    lbPrev.addEventListener('click', function () { show(index - 1); });
    lbNext.addEventListener('click', function () { show(index + 1); });

    // Bấm ra vùng tối bên ngoài ảnh để đóng
    lb.addEventListener('click', function (e) {
      if (e.target === lb || e.target.classList.contains('lb-stage')) close();
    });

    // Phím ← → để chuyển ảnh (Esc do <dialog> tự xử lý)
    lb.addEventListener('keydown', function (e) {
      if (album.photos.length < 2) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
    });

    // Vuốt trái / phải trên điện thoại
    var startX = null;
    var stage = lb.querySelector('.lb-stage');
    stage.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    stage.addEventListener('touchend', function (e) {
      if (startX === null || album.photos.length < 2) return;
      var dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
      startX = null;
    });

    // "Đặt buổi chụp như thế này": đóng popup, chọn sẵn loại chụp rồi cuộn tới form
    lbBook.addEventListener('click', function () {
      var select = document.querySelector('#booking-form select[name="service"]');
      if (select && album) {
        [].forEach.call(select.options, function (o) {
          if (o.text === album.service) select.value = o.value;
        });
      }
      opener = null; // không trả focus về card, để trang cuộn xuống form
      close();
    });
  }

  /* ---------- Hình & chữ hiện dần khi cuộn tới ---------- */
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // [selector, kiểu, trễ ban đầu (giây), trễ giữa các phần tử cùng nhóm (giây)]
  // kiểu:  img = hình mở ra  |  split = chữ chạy lên từng từ  |  up = trồi lên
  //        fade-left / fade-right = trượt vào từ trái / phải
  var groups = [
    // Hero
    ['.hero .eyebrow', 'fade-left', 0],
    ['.hero h1', 'split', 0.1],
    ['.hero .desc', 'up', 0.8],
    ['.hero .actions', 'up', 0.95],
    ['.hero .edition', 'up', 1.1],
    ['.hero .right .ph', 'img', 0.15],
    ['.hero .cap', 'up', 1.0],
    // Giá trị
    ['.values .item', 'fade-left', 0, 0.12],
    // Bộ ảnh
    ['.stories .label', 'fade-left', 0],
    ['.stories .h2', 'split', 0.1],
    ['.stories .more', 'up', 0.3],
    ['.card .ph', 'img', 0, 0.15],
    ['.card h3', 'up', 0.35, 0.15],
    ['.card p', 'up', 0.45, 0.15],
    // Về Mộc
    ['.about .ph', 'img', 0],
    ['.about .label', 'fade-left', 0.1],
    ['.about .h2', 'split', 0.2],
    ['.about .p p', 'up', 0.5, 0.15],
    ['.about .link', 'up', 0.9],
    // Dịch vụ
    ['.services .label', 'fade-left', 0],
    ['.services .h2', 'split', 0.1],
    ['.services .note', 'up', 0.3],
    ['.pkg', 'up', 0, 0.15],
    // Quy trình
    ['.process h2', 'split', 0],
    ['.step', 'up', 0, 0.15],
    // Lời nhắn
    ['.kind .label', 'up', 0],
    ['.kind blockquote', 'split', 0.1],
    ['.kind .who', 'up', 1.0],
    ['.kind .cap', 'up', 1.15],
    // Đặt lịch
    ['.booking .label', 'fade-left', 0],
    ['.booking h2', 'split', 0.1],
    ['.booking .desc', 'up', 0.4],
    ['.booking .direct', 'up', 0.55],
    ['.booking .contact', 'up', 0.7],
    ['.booking form', 'fade-right', 0.2],
    // Footer
    ['.foot .top > *', 'up', 0, 0.12],
    ['.foot .bot', 'up', 0.2]
  ];

  // Tách chữ thành từng từ (giữ nguyên thẻ <br>) để mỗi từ chạy lên lần lượt
  function splitWords(el) {
    el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());
    var n = 0;
    (function walk(node) {
      [].slice.call(node.childNodes).forEach(function (c) {
        if (c.nodeType === 3) {
          var frag = document.createDocumentFragment();
          c.textContent.split(/(\s+)/).forEach(function (part) {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span');
            w.className = 'w';
            w.setAttribute('aria-hidden', 'true');
            var i = document.createElement('span');
            i.className = 'wi';
            i.style.setProperty('--i', n++);
            i.textContent = part;
            w.appendChild(i);
            frag.appendChild(w);
          });
          node.replaceChild(frag, c);
        } else if (c.nodeType === 1 && c.tagName !== 'BR') {
          walk(c);
        }
      });
    })(el);
    el.style.setProperty('--s', Math.min(0.07, 0.9 / Math.max(n, 1)).toFixed(3) + 's');
  }

  var targets = [];
  groups.forEach(function (g) {
    document.querySelectorAll(g[0]).forEach(function (el, i) {
      var type = g[1];
      if (type === 'img') {
        el.classList.add('img-reveal');
      } else if (type === 'split') {
        el.classList.add('split');
        if (!reduce) splitWords(el);
      } else {
        el.classList.add('reveal');
        if (type !== 'up') el.classList.add(type);
      }
      el.style.setProperty('--d', ((g[2] || 0) + i * (g[3] || 0)).toFixed(2) + 's');
      targets.push(el);
    });
  });

  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('is-in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    targets.forEach(function (el) { io.observe(el); });
  } else {
    targets.forEach(function (el) { el.classList.add('is-in'); });
  }

  /* ---------- Hiệu ứng khi cuộn: tiến trình, menu, parallax, nút nổi, scrollspy ---------- */
  var nav = document.querySelector('.nav');
  var bar = document.querySelector('.progress');
  var floatBox = document.querySelector('.float');
  var parallaxImgs = [].slice.call(document.querySelectorAll('.hero .ph img, .about .ph img'));

  var spy = [
    ['#top', '.hero'], ['#bo-anh', '#bo-anh'], ['#moc', '#moc'], ['#dich-vu', '#dich-vu']
  ].map(function (p) {
    return { link: document.querySelector('.nav ul a[href="' + p[0] + '"]'), el: document.querySelector(p[1]) };
  }).filter(function (s) { return s.link && s.el; });

  var lastY = window.pageYOffset, ticking = false;

  function update() {
    ticking = false;
    var y = window.pageYOffset;
    var max = document.documentElement.scrollHeight - window.innerHeight;

    bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
    nav.classList.toggle('is-scrolled', y > 10);

    var delta = y - lastY;
    if (Math.abs(delta) > 6) {
      nav.classList.toggle('is-hidden', delta > 0 && y > 200);
      lastY = y;
    }
    if (y < 200) nav.classList.remove('is-hidden');

    if (floatBox) floatBox.classList.toggle('show', y > 400);

    var current = spy[0];
    spy.forEach(function (s) {
      if (s.el.getBoundingClientRect().top <= window.innerHeight * 0.4) current = s;
    });
    spy.forEach(function (s) { s.link.classList.toggle('active', s === current); });

    if (!reduce) {
      parallaxImgs.forEach(function (img) {
        var r = img.parentNode.getBoundingClientRect();
        var offset = (r.top + r.height / 2 - window.innerHeight / 2) * -0.06;
        img.style.setProperty('--py', Math.max(-22, Math.min(22, offset)).toFixed(1) + 'px');
      });
    }
  }

  function onScroll() {
    if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();