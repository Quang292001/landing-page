document.getElementById('booking-form').addEventListener('submit', function (e) {
  e.preventDefault();
  var f = e.target, ok = true;
  f.querySelectorAll('[required]').forEach(function (el) {
    var bad = !el.value.trim();
    el.style.borderBottomColor = bad ? '#e0a38f' : '';
    if (bad) ok = false;
  });
  if (!ok) return;
  // TODO: gửi dữ liệu tới backend / Google Form / Formspree tại đây
  document.getElementById('ok').style.display = 'block';
  f.reset();
});
