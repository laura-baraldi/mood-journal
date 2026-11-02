<script setup lang="ts">
defineProps<{
  eyebrow: string;
  title: string;
  subtitle: string;
}>();

const { t, locale, setLocale } = useI18n();
</script>

<template>
  <div class="auth-page">
    <div class="language-switch" aria-label="Choose language">
      <button
        type="button"
        :class="{ active: locale === 'en' }"
        :aria-pressed="locale === 'en'"
        @click="setLocale('en')"
      >
        EN
      </button>

      <button
        type="button"
        :class="{ active: locale === 'sv' }"
        :aria-pressed="locale === 'sv'"
        @click="setLocale('sv')"
      >
        SV
      </button>
    </div>

    <main class="auth-wrap">
      <div class="brand">
        <div class="brand-icon">✳</div>
        <span>{{ t("app.name") }}</span>
      </div>

      <section class="auth-card">
        <span class="card-sparkle">✳</span>

        <p class="eyebrow">{{ eyebrow }}</p>
        <h1>{{ title }}</h1>
        <p class="subtitle">{{ subtitle }}</p>

        <slot />

        <p class="auth-switch">
          <slot name="footer" />
        </p>
      </section>

      <footer class="auth-footer">
        {{ t("dashboard.footer") }} <span>♡</span>
      </footer>
    </main>
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap");

:root {
  font-family: "DM Sans", sans-serif;
  color: #303b36;
  background: #f7f8f4;
  font-synthesis: none;
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
}

button,
textarea,
input {
  font: inherit;
}
</style>

<style scoped>
.auth-page {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 16px;
  background: #f7f8f4;
}

.language-switch {
  position: absolute;
  top: 24px;
  right: 28px;
  display: flex;
  gap: 3px;
  padding: 4px;
  border: 1px solid #e7ebe4;
  border-radius: 30px;
  background: white;
}

.language-switch button {
  border: 0;
  border-radius: 20px;
  padding: 7px 10px;
  background: transparent;
  color: #89948a;
  font-size: 10px;
  font-weight: 700;
  cursor: pointer;
}

.language-switch button.active {
  background: #edf3eb;
  color: #56745e;
}

.auth-wrap {
  width: min(430px, 100%);
}

.brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 11px;
  margin-bottom: 24px;
  font-family: "Manrope", sans-serif;
  font-size: 18px;
  font-weight: 800;
}

.brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: #e9f0e8;
  color: #68856d;
  font-size: 24px;
}

.auth-card {
  position: relative;
  overflow: hidden;
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
  padding: 30px 28px 26px;
}

.card-sparkle {
  position: absolute;
  top: 22px;
  right: 24px;
  color: #b9c9b4;
  font-size: 27px;
}

.eyebrow {
  margin: 0 0 9px;
  font-size: 10px;
  letter-spacing: 1.5px;
  font-weight: 700;
  color: #99a59a;
}

h1 {
  margin: 0 0 7px;
  font-family: "Manrope", sans-serif;
  font-size: 27px;
  letter-spacing: -0.8px;
}

.subtitle {
  margin: 0 0 24px;
  color: #909b91;
  font-size: 13px;
}

.auth-switch {
  margin: 20px 0 0;
  text-align: center;
  color: #909b91;
  font-size: 12px;
}

.auth-footer {
  margin-top: 20px;
  text-align: center;
  color: #a0aaa0;
  font-size: 10px;
}

.auth-footer span {
  color: #91aa8e;
}

@media (max-width: 600px) {
  .auth-page {
    align-items: flex-start;
    padding-top: 72px;
  }

  .language-switch {
    top: 16px;
    right: 16px;
  }

  .auth-card {
    padding: 24px 20px 22px;
  }
}
</style>
