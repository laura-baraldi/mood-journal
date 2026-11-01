<script setup lang="ts">
const week = [
  { day: "Mon", emoji: "🙂", value: 65 },
  { day: "Tue", emoji: "😄", value: 90 },
  { day: "Wed", emoji: "😐", value: 45 },
  { day: "Thu", emoji: "😕", value: 30 },
  { day: "Fri", emoji: "🙂", value: 70 },
  { day: "Sat", emoji: "😄", value: 95 },
  { day: "Sun", emoji: "—", value: 0 },
];
</script>

<template>
  <section class="card week-card">
    <div class="section-heading">
      <div>
        <p class="eyebrow">YOUR JOURNEY</p>
        <h2>This week</h2>
      </div>
      <span class="week-caption">Last 7 days</span>
    </div>

    <div class="week-chart">
      <div v-for="day in week" :key="day.day" class="chart-day">
        <div class="chart-bar-area">
          <div
            v-if="day.value"
            class="chart-bar"
            :style="{ height: `${day.value}%` }"
          ></div>
          <span v-else class="empty-bar">—</span>
        </div>
        <span class="chart-emoji">{{ day.emoji }}</span>
        <span class="chart-label">{{ day.day }}</span>
      </div>
    </div>
  </section>
</template>

<style scoped>
.card {
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
  padding: 25px;
}

.section-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.eyebrow {
  font-size: 10px;
  letter-spacing: 1.5px;
  font-weight: 700;
  color: #99a59a;
  margin-bottom: 9px;
}

h2 {
  font-family: "Manrope", sans-serif;
  font-size: 19px;
  margin: 0 0 7px;
}

.week-caption {
  color: #a0aaa0;
  font-size: 11px;
}

.week-chart {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 12px;
  margin-top: 22px;
}

.chart-day {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.chart-bar-area {
  height: 90px;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  background: #f6f8f4;
  border-radius: 8px;
  overflow: hidden;
}

.chart-bar {
  width: 55%;
  background: #a9c0a6;
  border-radius: 6px 6px 0 0;
}

.empty-bar {
  color: #c5cec3;
  margin-bottom: 8px;
}

.chart-emoji {
  font-size: 18px;
}

.chart-label {
  font-size: 10px;
  color: #99a49a;
}
</style>
