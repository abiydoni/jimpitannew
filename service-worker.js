let CACHE_NAME = "jimpitan-cache-v1.5"; // Nama cache default

self.addEventListener("install", (event) => {
  console.log("[Service Worker] Install Event");

  event.waitUntil(
    fetch("/get_cache_version.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Failed to fetch cache name");
        }
        return response.json();
      })
      .then((data) => {
        CACHE_NAME = data.cache_name || CACHE_NAME;
        console.log("[Service Worker] Cache Name Set:", CACHE_NAME);

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
  event.respondWith(
    caches
      .match(event.request)
      .then((response) => {
        return response || fetch(event.request);
      })
      .catch((error) => {
        console.error("[Service Worker] Fetch failed:", error);
        throw error;
      })
  );
});

self.addEventListener("activate", (event) => {
  console.log("[Service Worker] Activate Event");

  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log("[Service Worker] Deleting old cache:", cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .catch((error) => {
        console.error("[Service Worker] Activation failed:", error);
      })
  );
});
