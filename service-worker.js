let CACHE_NAME = "jimpitan-cache-v1.5"; // Nama cache default jika gagal mengambil dari server

self.addEventListener("install", (event) => {
  console.log("[Service Worker] Install Event");

  // Ambil nama cache dari endpoint
  event.waitUntil(
    fetch("/get_cache_version.php") // Endpoint untuk mendapatkan nama cache
      .then((response) => {
        if (!response.ok) {
          throw new Error("Failed to fetch cache name");
        }
        return response.json();
      })
      .then((data) => {
        CACHE_NAME = data.cache_name || CACHE_NAME; // Gunakan nama dari server, fallback ke default
        console.log("[Service Worker] Cache Name Set:", CACHE_NAME);

        // Buka cache dan tambahkan file yang akan dicache
        return caches.open(CACHE_NAME).then((cache) => {
          console.log("[Service Worker] Caching Files");
          return cache.addAll([
            "/", // Tambahkan file yang perlu dicache
            "index.php",
            "login.php",
            "manifest.json",
            "assets/audio/interface.wav",
            "assets/image/block.gif",
          ]);
        });
      })
      .catch((error) => {
        console.error("[Service Worker] Failed to set cache name:", error);
      })
  );
});

self.addEventListener("fetch", (event) => {
  // Respon dari cache jika ada, jika tidak ambil dari jaringan
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});

self.addEventListener("activate", (event) => {
  console.log("[Service Worker] Activate Event");

  // Hapus cache lama jika ada versi baru
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log("[Service Worker] Deleting old cache:", cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
