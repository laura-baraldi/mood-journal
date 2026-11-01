<script setup lang="ts">
const props = defineProps<{
  selectedMood: string;
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

const currentMood = computed(() => ({
  emoji: moods[props.selectedMood] ?? "—",
  label: moods[props.selectedMood]
    ? t(`moods.${props.selectedMood}`)
    : t("dashboard.notSelected"),
}));
</script>

<template>
  <section class="card summary-card">
    <p class="eyebrow">{{ t("dashboard.summary") }}</p>
    <h2>{{ t("dashboard.yourMood") }}</h2>

    <div class="summary-mood">
      <span>{{ currentMood.emoji }}</span>
      <div>
        <strong>{{ currentMood.label }}</strong>
        <small>{{ t("dashboard.selectedMood") }}</small>
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
  margin: 0 0 24px;
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
