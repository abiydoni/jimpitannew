<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Custom Emoji Picker Toggle</title>
<style>
  body {
    font-family: Arial, sans-serif;
    padding: 1rem;
  }
  #emoji-picker {
    border: 1px solid #ddd;
    padding: 10px;
    max-width: 300px;
    max-height: 150px;
    overflow-y: auto;
    display: none; /* awalnya disembunyikan */
    flex-wrap: wrap;
    gap: 5px;
    background: #fff;
    position: absolute;
    z-index: 1000;
  }
  .emoji {
    font-size: 24px;
    cursor: pointer;
    user-select: none;
    padding: 3px;
    border-radius: 4px;
    transition: background-color 0.2s;
  }
  .emoji:hover {
    background-color: #eee;
  }
  textarea {
    width: 100%;
    height: 100px;
    margin-top: 1rem;
    font-size: 18px;
    padding: 8px;
  }
  #emoji-button {
    cursor: pointer;
    padding: 6px 12px;
    background-color: #eee;
    border: 1px solid #ccc;
    border-radius: 6px;
    user-select: none;
  }
</style>
</head>
<body>

<h2>Klik tombol untuk buka/tutup emoji picker</h2>

<button id="emoji-button">😃 Emoji</button>

<div id="emoji-picker" aria-label="Emoji picker"></div>

<textarea id="message" placeholder="Tulis pesan di sini..."></textarea>

<script>
const emojis = [
  "😀","😁","😂","🤣","😃","😄","😅","😆","😉","😊",
  "😋","😎","😍","😘","😗","😙","😚","🙂","🤗","🤩",
  "🤔","🤨","😐","😑","😶","🙄","😏","😣","😥","😮",
  "🤐","😯","😪","😫","😴","😌","😛","😜","😝","🤤",
  "😒","😓","😔","😕","🙃","🤑","😲","☹️","🙁","😖",
  "😞","😟","😤","😢","😭","😦","😧","😨","😩","🤯",
  "😬","😰","😱","🥵","🥶","😳","🤪","😵","😡","😠",
  "🤬","😷","🤒","🤕","🤢","🤮","🤧","😇","🤠","🥳"
];

const picker = document.getElementById('emoji-picker');
const button = document.getElementById('emoji-button');
const textarea = document.getElementById('message');

// Render emojis di picker
function renderEmojis() {
  picker.innerHTML = '';
  emojis.forEach(e => {
    const span = document.createElement('span');
    span.textContent = e;
    span.className = 'emoji';
    span.title = `Emoji: ${e}`;
    span.addEventListener('click', () => {
      // Masukkan emoji di posisi kursor textarea
      const start = textarea.selectionStart;
      const end = textarea.selectionEnd;
      const text = textarea.value;
      textarea.value = text.substring(0, start) + e + text.substring(end);
      textarea.selectionStart = textarea.selectionEnd = start + e.length;
      textarea.focus();
    });
    picker.appendChild(span);
  });
}

renderEmojis();

button.addEventListener('click', () => {
  if (picker.style.display === 'flex') {
    picker.style.display = 'none';
  } else {
    // posisi picker di bawah tombol
    const rect = button.getBoundingClientRect();
    picker.style.position = 'absolute';
    picker.style.top = (rect.bottom + window.scrollY) + 'px';
    picker.style.left = (rect.left + window.scrollX) + 'px';
    picker.style.display = 'flex';
  }
});

// Klik di luar picker dan button -> tutup picker
document.addEventListener('click', (e) => {
  if (!picker.contains(e.target) && e.target !== button) {
    picker.style.display = 'none';
  }
});
</script>

</body>
</html>
