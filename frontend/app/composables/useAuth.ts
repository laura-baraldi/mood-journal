export function useAuth() {
  const config = useRuntimeConfig();

  async function logout() {
    // Get the CSRF cookie from Laravel Sanctum.
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    const xsrfCookie = useCookie("XSRF-TOKEN");

    // End the authenticated Laravel session.
    await $fetch("/api/logout", {
      baseURL: config.public.apiBase,
      method: "POST",
      credentials: "include",
      headers: {
        Accept: "application/json",
        "X-XSRF-TOKEN": xsrfCookie.value
          ? decodeURIComponent(xsrfCookie.value)
          : "",
      },
    });

    await navigateTo("/login");
  }

  return {
    logout,
  };
}
