<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Emoji Picker Test</title>
  <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.min.js"></script>
  <style>
    body {
      font-family: sans-serif;
      padding: 20px;
    }
    textarea {
      width: 100%;
      height: 100px;
      padding: 10px;
    }
    #emoji-button {
      margin-top: 10px;
      padding: 6px 10px;
      background-color: #f0f0f0;
      border: 1px solid #ccc;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h2>Emoji Picker Demo</h2>
  
  <textarea id="message" placeholder="Tulis pesan..."></textarea><br>
  <button id="emoji-button">😊 Emoji</button>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const button = document.querySelector('#emoji-button');
      const textarea = document.querySelector('#message');
      const picker = new EmojiButton();

      picker.on('emoji', emoji => {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + emoji + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
        textarea.focus();
      });

      button.addEventListener('click', () => {
        picker.togglePicker(button);
      });
    });
  </script>

</body>
</html>
