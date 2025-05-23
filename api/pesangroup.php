<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Contoh Emoji Picker</title>
  <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
  <style>
    body {
      font-family: sans-serif;
      padding: 2rem;
    }
    textarea {
      width: 100%;
      height: 150px;
      margin-top: 10px;
    }
    #emoji-button {
      margin-bottom: 10px;
      padding: 8px 12px;
      background-color: #ddd;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h2>Contoh Emoji Picker</h2>
  <button id="emoji-button">😀 Emoji</button><br>
  <textarea id="message" placeholder="Tulis pesan di sini..."></textarea>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const button = document.querySelector('#emoji-button');
      const textarea = document.querySelector('#message');

      const picker = new EmojiButton({
        position: 'bottom-start',
        theme: 'light'
      });

      picker.on('emoji', emoji => {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        textarea.value = textarea.value.substring(0, start) + emoji + textarea.value.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
      });

      button.addEventListener('click', () => {
        picker.togglePicker(button);
      });
    });
  </script>

</body>
</html>
