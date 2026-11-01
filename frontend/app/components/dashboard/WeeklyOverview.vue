<script setup lang="ts">
const props = defineProps<{
  entries: {
    id: number;
    mood: string;
    created_at: string;
  }[];
}>();

const { t } = useI18n();

const moodScores: Record<string, number> = {
  sad: 20,
  lonely: 25,
  anxious: 30,
  frustrated: 25,
  angry: 20,
  tired: 35,
  neutral: 50,
  calm: 70,
  happy: 85,
  excited: 100,
};

const moodEmojis: Record<string, string> = {
  sad: "😢",
  lonely: "😔",
  anxious: "😰",
  frustrated: "😤",
  angry: "😡",
  tired: "😴",
  neutral: "😐",
  calm: "😌",
  happy: "😊",
  excited: "🤩",
};

const week = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  return Array.from({ length: 7 }, (_, index) => {
    const date = new Date(today);
    date.setDate(today.getDate() - (6 - index));

    const dayEntries = props.entries.filter((entry) => {
      const entryDate = new Date(entry.created_at);
      return entryDate.toDateString() === date.toDateString();
    });

    const scoredEntries = dayEntries.filter(
      (entry) => moodScores[entry.mood] !== undefined,
    );

    const average = scoredEntries.length
      ? scoredEntries.reduce(
          (total, entry) => total + (moodScores[entry.mood] ?? 0),
          0,
        ) / scoredEntries.length
      : 0;

    const closestMood = Object.entries(moodScores).reduce(
      (closest, [mood, score]) =>
        Math.abs(score - average) < Math.abs(closest.score - average)
          ? { mood, score }
          : closest,
      { mood: "", score: Infinity },
    );

    const weekdayKeys = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];

    return {
      day: weekdayKeys[date.getDay()],
      emoji: scoredEntries.length ? (moodEmojis[closestMood.mood] ?? "—") : "—",
      value: average,
      count: scoredEntries.length,
    };
  });
});

const weeklyAverage = computed(() => {
  const totalEntries = week.value.reduce((total, day) => total + day.count, 0);

  if (!totalEntries) return null;

  const totalScore = week.value.reduce(
    (total, day) => total + day.value * day.count,
    0,
  );

  const average = totalScore / totalEntries;

  const closestMood = Object.entries(moodScores).reduce(
    (closest, [mood, score]) =>
      Math.abs(score - average) < Math.abs(closest.score - average)
        ? { mood, score }
        : closest,
    { mood: "", score: Infinity },
  );

  return {
    mood: closestMood.mood,
    emoji: moodEmojis[closestMood.mood] ?? "—",
    label: t(`moods.${closestMood.mood}`),
  };
});
</script>

<template>
  <section class="card week-card">
    <div class="section-heading">
      <div>
        <p class="eyebrow">{{ t("dashboard.weekly") }}</p>
        <h2>{{ t("dashboard.thisWeek") }}</h2>
      </div>

      <div class="heading-right">
        <span class="week-caption">{{ t("dashboard.lastSevenDays") }}</span>

        <div v-if="weeklyAverage" class="weekly-average">
          <span>{{ t("dashboard.weeklyAverage") }}</span>
          <div class="average-mood">
            <span>{{ weeklyAverage.emoji }}</span>
            <strong>{{ weeklyAverage.label }}</strong>
          </div>
        </div>
      </div>
    </div>

    <div class="week-chart">
      <div v-for="(day, index) in week" :key="index" class="chart-day">
        <div class="chart-bar-area">
          <div
            v-if="day.value"
            class="chart-bar"
            :style="{ height: `${day.value}%` }"
            :title="`${day.count} check-in(s)`"
          ></div>
          <span v-else class="empty-bar">—</span>
        </div>

        <span class="chart-emoji">{{ day.emoji }}</span>
        <span class="chart-label">{{ t(`weekdays.${day.day}`) }}</span>
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

.heading-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
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

.weekly-average {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  color: #99a59a;
  font-size: 10px;
}

.average-mood {
  display: flex;
  align-items: center;
  gap: 6px;
}

.average-mood span {
  font-size: 20px;
}

.average-mood strong {
  color: #607d60;
  font-size: 13px;
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
