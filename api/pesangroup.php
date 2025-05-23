<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manual Emoji Picker</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 2rem;
  }
  #emoji-picker {
    display: none;
    border: 1px solid #ccc;
    padding: 10px;
    width: 200px;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    position: absolute;
    z-index: 1000;
  }
  #emoji-picker span {
    font-size: 24px;
    cursor: pointer;
    padding: 5px;
    user-select: none;
  }
  #emoji-button {
    cursor: pointer;
    padding: 0.5rem 1rem;
    background-color: #ddd;
    border: none;
    border-radius: 6px;
  }
  #message {
    width: 100%;
    height: 100px;
    margin-top: 1rem;
    padding: 0.5rem;
    font-size: 1rem;
  }
</style>
</head>
<body>

<button id="emoji-button">😀 Emoji</button><br />
<div id="emoji-picker" aria-label="Pilih emoji">
  <span>😀</span><span>😂</span><span>😍</span><span>👍</span><span>🙏</span>
  <span>😎</span><span>🎉</span><span>🔥</span><span>🤔</span><span>😢</span>
</div>
<textarea id="message" placeholder="Tulis pesan..."></textarea>

<script>
  const button = document.getElementById('emoji-button');
  const picker = document.getElementById('emoji-picker');
  const textarea = document.getElementById('message');

  // Toggle tampil/tidaknya picker
  button.addEventListener('click', () => {
    if (picker.style.display === 'block') {
      picker.style.display = 'none';
    } else {
      // posisi picker di bawah tombol
      const rect = button.getBoundingClientRect();
      picker.style.top = rect.bottom + window.scrollY + 'px';
      picker.style.left = rect.left + window.scrollX + 'px';
      picker.style.display = 'block';
    }
  });

  // Klik emoji, sisipkan ke textarea
  picker.addEventListener('click', e => {
    if (e.target.tagName === 'SPAN') {
      const emoji = e.target.textContent;
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      textarea.value = textarea.value.substring(0, start) + emoji + textarea.value.substring(end);
      textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
      textarea.focus();
      picker.style.display = 'none'; // sembunyikan picker setelah pilih emoji
    }
  });

  // Klik di luar picker dan tombol akan menutup picker
  document.addEventListener('click', e => {
    if (!picker.contains(e.target) && e.target !== button) {
      picker.style.display = 'none';
    }
  });
</script>

</body>
</html>
