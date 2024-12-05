const CACHE_NAME = "jimpitan-cache-v5"; // Nama cache dengan versi tetap
const urlsToCache = [
  "/",
  "index.php",
  "login.php",
  "manifest.json",
  "assets/audio/interface.wav",
  "assets/image/block.gif",
];

// Install event: Simpan file ke cache
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
  );
  console.log("Service Worker: Installed and files cached.");
});

// Activate event: Hapus cache lama
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) =>
        Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log(`Service Worker: Deleting old cache: ${cacheName}`);
              return caches.delete(cacheName); // Hapus cache lama
            }
          })
        )
      )
      .then(() => {
        console.log("Service Worker: Cache updated.");
        return self.clients.claim(); // Ambil kendali halaman
      })
  );
});

// Fetch event: Gunakan cache atau fetch dari jaringan
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return (
        response ||
        fetch(event.request).catch(() => {
          // Fallback jika fetch gagal
          if (event.request.mode === "navigate") {
            return caches.match("/"); // Redirect ke halaman utama jika offline
          }
        })
      );
    })
  );
});

// Kirim notifikasi ke pengguna ketika cache diperbarui
self.addEventListener("message", (event) => {
  if (event.data === "CHECK_UPDATE") {
    event.waitUntil(
      caches.keys().then((cacheNames) => {
        if (!cacheNames.includes(CACHE_NAME)) {
          self.clients.matchAll().then((clients) => {
            clients.forEach((client) => {
              client.postMessage(
                "Cache updated. Please reload to get the latest version."
              );
            });
          });
        }
      })
    );
  }
});
