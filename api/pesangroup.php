<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Custom Emoji Picker Simple</title>
<style>
  body {
    font-family: Arial, sans-serif;
    padding: 1rem;
  }
  #emoji-picker {
    border: 1px solid #ddd;
    padding: 10px;
    max-width: 300px;
    height: 150px;
    overflow-y: scroll;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    background: #fff;
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
</style>
</head>
<body>

<h2>Custom Emoji Picker Simple</h2>

<div id="emoji-picker"></div>

<textarea id="message" placeholder="Tulis pesan di sini..."></textarea>

<script>
// Daftar emoji (basic subset, bisa kamu tambah sendiri sesuai kebutuhan)
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

// Render emoji ke div picker
const picker = document.getElementById('emoji-picker');

emojis.forEach(e => {
  const span = document.createElement('span');
  span.textContent = e;
  span.className = 'emoji';
  span.title = `Emoji: ${e}`;
  span.addEventListener('click', () => {
    const textarea = document.getElementById('message');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    // Masukkan emoji di posisi kursor
    textarea.value = text.substring(0, start) + e + text.substring(end);
    // Pindahkan kursor setelah emoji
    textarea.selectionStart = textarea.selectionEnd = start + e.length;
    textarea.focus();
  });
  picker.appendChild(span);
});
</script>

</body>
</html>
