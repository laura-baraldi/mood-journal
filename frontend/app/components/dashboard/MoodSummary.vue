<script setup lang="ts">
const props = defineProps<{
  selectedMood: string;
}>();

const moods: Record<string, { emoji: string; label: string }> = {
  sad: { emoji: "😢", label: "Sad" },
  lonely: { emoji: "😔", label: "Lonely" },
  anxious: { emoji: "😰", label: "Anxious" },
  frustrated: { emoji: "😤", label: "Frustrated" },
  angry: { emoji: "😡", label: "Angry" },
  tired: { emoji: "😴", label: "Tired" },
  neutral: { emoji: "😐", label: "Neutral" },
  calm: { emoji: "😌", label: "Calm" },
  happy: { emoji: "😊", label: "Happy" },
  excited: { emoji: "🤩", label: "Excited" },
};

const currentMood = computed(
  () =>
    moods[props.selectedMood] ?? {
      emoji: "—",
      label: "Not selected",
    },
);
</script>

<template>
  <section class="card summary-card">
    <p class="eyebrow">TODAY'S SUMMARY</p>
    <h2>Your mood</h2>

    <div class="summary-mood">
      <span>{{ currentMood.emoji }}</span>
      <div>
        <strong>{{ currentMood.label }}</strong>
        <small>Selected mood</small>
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
