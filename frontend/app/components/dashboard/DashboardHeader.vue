<script setup lang="ts">
defineProps<{
  name: string;
}>();

const today = new Date().toLocaleDateString("en-US", {
  weekday: "long",
  month: "long",
  day: "numeric",
});
</script>

<template>
  <header class="topbar">
    <div>
      <p class="eyebrow">YOUR PERSONAL SPACE</p>
      <h1>Good to see you, {{ name }} <span>✳</span></h1>
      <p class="subtitle">Take a moment for yourself today.</p>
    </div>

    <div class="date-pill">
      <span>◷</span>
      {{ today }}
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

  .date-pill {
    display: inline-block;
    margin-top: 17px;
  }
}
</style>
