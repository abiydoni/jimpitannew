<?php
include "cek_login.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
        <!-- Loader GIF loading -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
    </div>

<form action="login.php" method="POST">
<div class="screen-1">
    <div class="email">
        <label for="user_name">User</label>
        <div class="sec-2">
            <ion-icon name="person-outline"></ion-icon>
            <input type="text" name="user_name" placeholder="********" required/>
        </div>
    </div>
    
    <div class="password">
        <label for="password">Password</label>
        <div class="sec-2">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input class="pas" type="password" name="password" placeholder="********" required/>
        </div>
    </div>

    <div class="password">
    <label><input type="checkbox" name="redirect_option" value="dashboard"> Go To Dashboard</label>
    </div>

    <button class="login">Login</button>
    <div class="footer">
        <?php if ($error): ?>
            <div class="error-message" style="color: red; font-size: 12px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
    <p style="color:grey; font-size: 8px; text-align: center;">@2024 copyright | by doniabiy</p>
</div>
</form>
<script>
  // Menambahkan event listener untuk semua elemen tombol/link
  document.querySelectorAll('button, a, input[type="submit"]').forEach(element => {
    element.addEventListener('click', function (e) {
      // Mencegah form disubmit langsung atau link berpindah halaman
      e.preventDefault();

      // Tampilkan loader
      document.getElementById('loader').classList.remove('hidden');
      
      // Jika itu adalah form submit, submit form setelah beberapa detik
      if (this.type === 'submit') {
        setTimeout(function() {
          this.closest('form').submit();
        }.bind(this), 500); // Tunggu 500ms sebelum submit form
      } else {
        // Jika itu link, pindahkan halaman setelah beberapa detik
        setTimeout(() => {
          window.location.href = this.href;
        }, 500); // Tunggu 500ms sebelum pindah halaman
      }
    });
  });
</script>

</body>
</html>