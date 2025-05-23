<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Test Emoji Picker</title>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 2rem;
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
<textarea id="message" placeholder="Tulis pesan..."></textarea>

<script>
  const button = document.querySelector('#emoji-button');
  const textarea = document.querySelector('#message');
  const picker = new EmojiButton();

  picker.on('emoji', emoji => {
    // Simpan posisi cursor
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;

    // Sisipkan emoji di posisi cursor
    textarea.value = textarea.value.substring(0, start) + emoji + textarea.value.substring(end);

    // Atur posisi cursor setelah emoji
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;

    // Fokus textarea agar bisa ketik lagi
    textarea.focus();
  });

  button.addEventListener('click', () => {
    picker.togglePicker(button);
  });
</script>

</body>
</html>
