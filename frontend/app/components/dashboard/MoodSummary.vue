<script setup lang="ts">
const props = defineProps<{
  entries: {
    id: number;
    mood: string;
    created_at: string;
  }[];
}>();

const { t } = useI18n();

const moods: Record<string, string> = {
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

const todayEntry = computed(() => {
  const today = new Date();

  return (
    props.entries
      .filter((entry) => {
        const entryDate = new Date(entry.created_at);

        return (
          entryDate.getFullYear() === today.getFullYear() &&
          entryDate.getMonth() === today.getMonth() &&
          entryDate.getDate() === today.getDate()
        );
      })
      .sort(
        (a, b) =>
          new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
      )[0] ?? null
  );
});

const currentMood = computed(() => {
  const mood = todayEntry.value?.mood;

  return {
    emoji: mood ? (moods[mood] ?? "—") : "—",
    label: mood ? t(`moods.${mood}`) : t("dashboard.notSelected"),
    date: todayEntry.value
      ? new Date(todayEntry.value.created_at).toLocaleDateString()
      : "",
  };
});
</script>

<template>
  <section class="card summary-card">
    <p class="eyebrow">{{ t("dashboard.summary") }}</p>
    <h2>{{ t("dashboard.yourMood") }}</h2>

    <div class="summary-mood">
      <span>{{ currentMood.emoji }}</span>
      <div>
        <strong>{{ currentMood.label }}</strong>
        <small>
          {{ currentMood.date || t("dashboard.notSelected") }}
        </small>
      </div>
    </div>
  </section>
</template>

<style scoped>
.card {
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
  padding: 20px 22px;
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
  margin: 0 0 16px;
}

.summary-mood {
  display: flex;
  align-items: center;
  gap: 13px;
}

.summary-mood > span {
  width: 48px;
  height: 48px;
  display: grid;
  place-items: center;
  border-radius: 13px;
  background: #f0f5ed;
  font-size: 27px;
}

.summary-mood div {
  display: grid;
  gap: 5px;
}

.summary-mood strong {
  font-size: 14px;
}

.summary-mood small {
  font-size: 10px;
  color: #9aa49a;
}
</style>
