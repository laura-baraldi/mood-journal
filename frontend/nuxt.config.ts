export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },

  modules: ["@nuxtjs/i18n"],

  runtimeConfig: {
    // Server-side URL of the Laravel backend.
    // Overridden on Render by the NUXT_API_BASE environment variable.
    apiBase: "http://localhost:8000",

    public: {
      // All browser API requests go through the Nuxt proxy.
      apiBase: "/backend",
    },
  },

  i18n: {
    defaultLocale: "en",
    strategy: "prefix_except_default",

    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: "i18n_redirected",
      redirectOn: "root",
      alwaysRedirect: false,
    },

    locales: [
      {
        code: "en",
        name: "English",
        language: "en-US",
        file: "en.json",
      },
      {
        code: "sv",
        name: "Svenska",
        language: "sv-SE",
        file: "sv.json",
      },
    ],

    langDir: "locales",
  },
});
