<script setup lang="ts">
const { t } = useI18n();
</script>

<template>
  <div class="inspiration">
    <section class="quote-card">
      <span class="quote-mark">“</span>
      <p>{{ t("dashboard.quote") }}</p>
      <span class="quote-footer">{{ t("dashboard.quoteFooter") }}</span>
      <div class="quote-decoration">✳</div>
    </section>

    <section class="card tip-card">
      <div class="tip-icon">♡</div>
      <div>
        <strong>{{ t("dashboard.tipTitle") }}</strong>
        <p>{{ t("dashboard.tipDescription") }}</p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.inspiration {
  display: grid;
  gap: 20px;
}

.quote-card {
  position: relative;
  overflow: hidden;
  min-height: 195px;
  border-radius: 16px;
  padding: 23px;
  background: #eaf0e5;
  color: #58715b;
}

.quote-mark {
  font-family: Georgia, serif;
  font-size: 42px;
  line-height: 1;
  color: #9bb298;
}

.quote-card p {
  position: relative;
  z-index: 1;
  max-width: 190px;
  font-family: "Manrope", sans-serif;
  font-size: 17px;
  line-height: 1.5;
  font-weight: 700;
  margin: 7px 0 17px;
}

.quote-footer {
  font-size: 9px;
  letter-spacing: 1.3px;
  font-weight: 700;
  color: #829781;
}

.quote-decoration {
  position: absolute;
  right: -10px;
  bottom: -32px;
  font-size: 130px;
  color: #dce7d7;
}

.card {
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
  padding: 19px;
}

.tip-card {
  display: flex;
  gap: 13px;
  align-items: flex-start;
}

.tip-icon {
  width: 34px;
  height: 34px;
  flex-shrink: 0;
  border-radius: 10px;
  display: grid;
  place-items: center;
  background: #f4eee7;
  color: #b18b72;
}

.tip-card strong {
  font-size: 12px;
}

.tip-card p {
  color: #929d92;
  font-size: 11px;
  line-height: 1.6;
  margin: 6px 0 0;
}
</style>
