<script setup lang="ts">
type MoodEntry = {
  id: number;
  mood: string;
  note: string | null;
  created_at: string;
  updated_at?: string;
};

const props = defineProps<{
  entries: MoodEntry[];
  moods: Record<string, string>;
}>();

const { t, locale } = useI18n();

const groupedEntries = computed(() => {
  const groups = new Map<string, MoodEntry[]>();

  for (const entry of props.entries) {
    const date = new Date(entry.created_at);
    const key = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;

    if (!groups.has(key)) {
      groups.set(key, []);
    }

    groups.get(key)!.push(entry);
  }

  return Array.from(groups.entries()).map(([key, dayEntries]) => ({
    key,
    date: new Date(dayEntries[0]!.created_at),
    entries: dayEntries,
  }));
});

function formatDay(date: Date) {
  return new Intl.DateTimeFormat(locale.value, {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(date);
}

function formatTime(value: string) {
  return new Intl.DateTimeFormat(locale.value, {
    hour: "2-digit",
    minute: "2-digit",
  }).format(new Date(value));
}
</script>

<template>
  <div v-if="entries.length === 0" class="state-card empty-state">
    <span class="empty-icon">✳</span>
    <h3>{{ t("journal.emptyTitle") }}</h3>
    <p>{{ t("journal.emptyDescription") }}</p>
  </div>

  <div v-else class="timeline">
    <article v-for="group in groupedEntries" :key="group.key" class="day-group">
      <h3 class="day-heading">{{ formatDay(group.date) }}</h3>

      <div class="day-entries">
        <JournalEntryCard
          v-for="entry in group.entries"
          :key="entry.id"
          :mood-emoji="moods[entry.mood] ?? '—'"
          :mood-label="t(`moods.${entry.mood}`)"
          :time="formatTime(entry.created_at)"
          :note="entry.note"
          :no-note-label="t('journal.noNote')"
        />
      </div>
    </article>
  </div>
</template>

<style scoped>
.timeline {
  display: grid;
  gap: 28px;
}

.day-heading {
  margin: 0 0 13px;
  color: #718675;
  font-size: 13px;
  font-weight: 700;
  text-transform: capitalize;
}

.day-entries {
  display: grid;
  gap: 12px;
}

.state-card {
  padding: 45px 20px;
  text-align: center;
  border-radius: 12px;
  background: #fbfcfa;
}

.empty-icon {
  color: #8fa88f;
  font-size: 30px;
}

.state-card h3 {
  margin: 14px 0 8px;
  font-family: "Manrope", sans-serif;
  color: #303b36;
  font-size: 17px;
}

.state-card p {
  color: #929d93;
  font-size: 13px;
  line-height: 1.6;
}
</style>
