<script setup lang="ts">
defineProps<{
  name: string;
}>();

const { t, locale, setLocale } = useI18n();

const today = computed(() =>
  new Intl.DateTimeFormat(locale.value === "sv" ? "sv-SE" : "en-US", {
    weekday: "long",
    month: "long",
    day: "numeric",
  }).format(new Date()),
);
</script>

<template>
  <header class="topbar">
    <div>
      <p class="eyebrow">{{ t("dashboard.eyebrow") }}</p>
      <h1>{{ t("dashboard.greeting", { name }) }} <span>✳</span></h1>
      <p class="subtitle">{{ t("dashboard.subtitle") }}</p>
    </div>

    <div class="header-actions">
      <div class="language-switch" aria-label="Choose language">
        <button
          :class="{ active: locale === 'en' }"
          :aria-pressed="locale === 'en'"
          @click="setLocale('en')"
        >
          EN
        </button>

        <button
          :class="{ active: locale === 'sv' }"
          :aria-pressed="locale === 'sv'"
          @click="setLocale('sv')"
        >
          SV
        </button>
      </div>

      <div class="date-pill">
        <span>◷</span>
        {{ today }}
      </div>
    </div>
  </header>
</template>

<style scoped>
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.eyebrow {
  font-size: 10px;
  letter-spacing: 1.5px;
  font-weight: 700;
  color: #99a59a;
  margin: 0 0 9px;
}

h1 {
  font-family: "Manrope", sans-serif;
  font-size: clamp(23px, 3vw, 31px);
  margin: 0 0 7px;
  letter-spacing: -1px;
}

h1 span {
  color: #83a184;
}

.subtitle {
  color: #909b91;
  font-size: 13px;
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.language-switch {
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

.date-pill {
  border: 1px solid #e7ebe4;
  background: white;
  border-radius: 30px;
  padding: 11px 16px;
  color: #78847a;
  font-size: 12px;
}

.date-pill span {
  margin-right: 7px;
}

@media (max-width: 600px) {
  .topbar {
    display: block;
  }

  .header-actions {
    flex-wrap: wrap;
    margin-top: 17px;
  }
}
</style>
