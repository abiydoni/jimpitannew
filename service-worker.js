const CACHE_NAME = `jimpitan-cache-${new Date().toISOString()}`; // Versi cache otomatis berdasarkan waktu
const urlsToCache = [
  "/",
  "index.php",
  "login.php",
  "manifest.json",
  "assets/audio/interface.wav",
  "assets/image/block.gif",
];

// Install event untuk menyimpan file ke cache
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Opened cache and adding files to cache");
      return cache.addAll(urlsToCache);
    })
  );
});

// Activate event untuk menghapus cache lama dan memastikan hanya versi terbaru yang digunakan
self.addEventListener("activate", (event) => {
  const cacheWhitelist = [CACHE_NAME]; // Menyimpan cache terbaru yang akan digunakan
  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (!cacheWhitelist.includes(cacheName)) {
              console.log(`Deleting outdated cache: ${cacheName}`);
              return caches.delete(cacheName); // Menghapus cache lama
            }
          })
        );
      })
      .then(() => {
        console.log("Cache has been updated and old caches removed.");
        self.clients.claim(); // Mengambil kontrol langsung atas halaman yang sedang berjalan

        // Mengirim pesan ke halaman (client) yang sedang berjalan
        self.clients.matchAll().then((clients) => {
          clients.forEach((client) => {
            client.postMessage("Cache has been updated!");
          });
        });
      })
  );
});

// Fetch event untuk menggunakan cache atau melakukan fetch dari jaringan
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return (
        response ||
        fetch(event.request).then((networkResponse) => {
          // Jika fetch berhasil, simpan ke cache untuk penggunaan berikutnya
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, networkResponse.clone());
          });
          return networkResponse;
        })
      );
    })
  );
});
