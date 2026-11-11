<script setup lang="ts">
defineProps<{
  totalEntries: number;
  daysTracked: number;
  latestMoodEmoji: string;
  latestMoodLabel: string;
}>();

const { t } = useI18n();
</script>

<template>
  <section class="stats-grid">
    <article class="stat-card">
      <span>{{ t("journal.totalEntries") }}</span>
      <strong>{{ totalEntries }}</strong>
    </article>

    <article class="stat-card">
      <span>{{ t("journal.daysTracked") }}</span>
      <strong>{{ daysTracked }}</strong>
    </article>

    <article class="stat-card">
      <span>{{ t("journal.latestMood") }}</span>
      <strong class="latest-mood">
        <span>{{ latestMoodEmoji }}</span>
        <span v-if="latestMoodLabel">{{ latestMoodLabel }}</span>
        <span v-else>—</span>
      </strong>
    </article>
  </section>
</template>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 36px;
}

.stat-card {
  min-width: 0;
  padding: 22px;
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 15px;
  display: flex;
  flex-direction: column;
  gap: 13px;
}

.stat-card > span {
  color: #89968b;
  font-size: 12px;
}

.stat-card > strong {
  color: #526f58;
  font-family: "Manrope", sans-serif;
  font-size: 27px;
}

.latest-mood {
  display: flex;
  align-items: center;
  gap: 9px;
  font-size: 17px !important;
}

.latest-mood span:first-child {
  font-size: 25px;
}

@media (max-width: 600px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .stat-card {
    padding: 16px;
  }

  .stat-card:last-child {
    grid-column: 1 / -1;
  }
}
</style>
