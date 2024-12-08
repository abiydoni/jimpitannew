const CACHE_NAME = "jimpitan-cache-v1.5";
const urlsToCache = [
  "/",
  "index.php",
  "login.php",
  "manifest.json",
  "assets/audio/interface.wav",
  "assets/image/block.gif",
];

self.addEventListener("install", (event) => {
  console.log("[Service Worker] Install Event: Installing cache...");

  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => {
        console.log("[Service Worker] Caching files:", urlsToCache);
        return cache.addAll(urlsToCache);
      })
      .catch((error) => {
        console.error("[Service Worker] Failed to open cache:", error);
      })
  );
});

self.addEventListener("fetch", (event) => {
  console.log(`[Service Worker] Fetching resource: ${event.request.url}`);

  event.respondWith(
    caches
      .match(event.request)
      .then((response) => {
        if (response) {
          console.log(`[Service Worker] Found in cache: ${event.request.url}`);
        } else {
          console.log(
            `[Service Worker] Not found in cache, fetching: ${event.request.url}`
          );
        }
        return response || fetch(event.request);
      })
      .catch((error) => {
        console.error("[Service Worker] Fetch failed:", error);
      })
  );
});
