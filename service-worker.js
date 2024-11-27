const CACHE_NAME = "jimpitan-cache-v1.3";
const urlsToCache = [
  "/",
  "index.php",
  "css/styles.css",
  "js/app.js",
  "login.php",
  "detail.php",
  "manifest.json",
  "assets/audio/interface.wav",
  "assets/image/block.gif",
];

// Instalasi Service Worker: Caching file yang dibutuhkan
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Caching file...");
      return cache.addAll(CACHE_URLS);
    })
  );
});

// Aktivasi Service Worker: Hapus cache lama jika versi berubah
self.addEventListener("activate", (event) => {
  const cacheWhitelist = [CACHE_NAME]; // Daftar cache yang valid

  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          // Menghapus cache lama jika tidak ada dalam whitelist
          if (!cacheWhitelist.includes(cacheName)) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Menangani request jaringan dan memberikan respons dari cache
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse; // Mengembalikan file dari cache
      }

      // Jika tidak ditemukan di cache, fetch dari jaringan
      return fetch(event.request).then((response) => {
        if (!response || response.status !== 200) {
          return response; // Jika ada kesalahan, kembalikan respons dari jaringan
        }

        const responseToCache = response.clone();

        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseToCache); // Cache file baru
        });

        return response;
      });
    })
  );
});
