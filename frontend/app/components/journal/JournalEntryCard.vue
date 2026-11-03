<script setup lang="ts">
defineProps<{
  moodEmoji: string;
  moodLabel: string;
  time: string;
  note: string | null;
  noNoteLabel: string;
}>();
</script>

<template>
  <article class="entry-card">
    <div class="entry-top">
      <div class="mood-icon">
        {{ moodEmoji }}
      </div>

      <div class="entry-info">
        <strong>{{ moodLabel }}</strong>
        <span>{{ time }}</span>
      </div>
    </div>

    <p v-if="note" class="entry-note">
      {{ note }}
    </p>

    <p v-else class="no-note">
      {{ noNoteLabel }}
    </p>
  </article>
</template>

<style scoped>
.entry-card {
  padding: 18px;
  background: #fbfcfa;
  border: 1px solid #edf0eb;
  border-radius: 12px;
}

.entry-top {
  display: flex;
  align-items: center;
  gap: 13px;
}

.mood-icon {
  width: 46px;
  height: 46px;
  display: grid;
  place-items: center;
  background: #eff5ed;
  border-radius: 12px;
  font-size: 25px;
  flex-shrink: 0;
}

.entry-info {
  display: grid;
  gap: 5px;
}

.entry-info strong {
  color: #526b57;
  font-size: 14px;
}

.entry-info span {
  color: #9aa49a;
  font-size: 11px;
}

.entry-note {
  margin: 15px 0 0;
  padding: 13px 15px;
  background: white;
  border-radius: 9px;
  color: #68756b;
  font-size: 13px;
  line-height: 1.7;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
}

.no-note {
  margin: 13px 0 0;
  color: #a3ada3;
  font-size: 11px;
  font-style: italic;
}
</style>
