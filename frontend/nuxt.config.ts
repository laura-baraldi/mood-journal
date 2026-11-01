export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },

  modules: ["@nuxtjs/i18n"],

  runtimeConfig: {
    public: {
      apiBase: "http://localhost:8000",
    },
  },

  i18n: {
    defaultLocale: "en",
    strategy: "prefix_except_default",

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
