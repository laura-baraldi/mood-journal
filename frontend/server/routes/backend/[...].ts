import { proxyRequest, getRequestURL } from "h3";

// API proxy: forwards requests from /backend/* to the Laravel backend.
// Keeps browser requests on the frontend domain and forwards cookies
// and headers required by Laravel Sanctum authentication.

export default defineEventHandler((event) => {
  const config = useRuntimeConfig();
  const requestUrl = getRequestURL(event);

  const backendUrl = new URL(
    requestUrl.pathname.replace(/^\/backend/, "") + requestUrl.search,
    config.apiBase,
  );

  return proxyRequest(event, backendUrl.toString());
});
