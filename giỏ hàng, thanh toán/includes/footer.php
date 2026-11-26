</main>
<div class="foot">© <?= date('Y') ?> Sân Thể Thao – đặt sân thể thao nhanh chóng</div></div>
<!-- Vẽ mã QR ngay trên trình duyệt từ nội dung do PHP tạo (data-qr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
document.querySelectorAll('[data-qr]').forEach(function (box) {
  try {
    var q = qrcode(0, 'M'); q.addData(box.getAttribute('data-qr')); q.make();
    box.innerHTML = q.createSvgTag({ cellSize: 4, margin: 0, scalable: true });
  } catch (err) {
    box.innerHTML = '<div class="muted" style="padding:16px">Không tạo được ảnh QR (cần internet để tải thư viện). Hãy chuyển khoản theo thông tin bên dưới.</div>';
  }
});
</script>
</body>
</html>
