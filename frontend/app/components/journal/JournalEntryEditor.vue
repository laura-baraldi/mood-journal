<script setup lang="ts">
const props = defineProps<{
  mood: string;
  note: string;
  isSaving: boolean;
  moods: Record<string, string>;
}>();

const emit = defineEmits<{
  "update:mood": [value: string];
  "update:note": [value: string];
  save: [];
  cancel: [];
}>();

const { t } = useI18n();

const availableMoods = [
  "sad",
  "lonely",
  "anxious",
  "frustrated",
  "angry",
  "tired",
  "neutral",
  "calm",
  "happy",
  "excited",
];
</script>

<template>
  <section class="editor">
    <h3>{{ t("journal.editTitle") }}</h3>

    <label for="edit-mood">{{ t("dashboard.question") }}</label>

    <div class="mood-options">
      <button
        v-for="item in availableMoods"
        :key="item"
        type="button"
        class="mood-option"
        :class="{ selected: mood === item }"
        @click="emit('update:mood', item)"
      >
        <span>{{ moods[item] }}</span>
        <small>{{ t(`moods.${item}`) }}</small>
      </button>
    </div>

    <label for="edit-note">{{ t("dashboard.noteLabel") }}</label>

    <textarea
      id="edit-note"
      :value="note"
      rows="4"
      @input="emit('update:note', ($event.target as HTMLTextAreaElement).value)"
    />

    <div class="actions">
      <button
        type="button"
        class="cancel-button"
        :disabled="isSaving"
        @click="emit('cancel')"
      >
        {{ t("journal.cancel") }}
      </button>

      <button
        type="button"
        class="save-button"
        :disabled="isSaving || !mood"
        @click="emit('save')"
      >
        {{ isSaving ? t("journal.saving") : t("journal.saveChanges") }}
      </button>
    </div>
  </section>
</template>

<style scoped>
.editor {
  padding: 22px;
  background: #fbfcfa;
  border: 1px solid #e7ede5;
  border-radius: 14px;
}

.editor h3 {
  margin: 0 0 20px;
  color: #526b57;
  font-size: 16px;
}

.editor label {
  display: block;
  margin: 16px 0 10px;
  color: #68756b;
  font-size: 12px;
  font-weight: 600;
}

.mood-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(75px, 1fr));
  gap: 8px;
}

.mood-option {
  display: grid;
  justify-items: center;
  gap: 5px;
  padding: 10px 5px;
  border: 1px solid #edf0eb;
  border-radius: 9px;
  background: white;
  cursor: pointer;
}

.mood-option.selected {
  border-color: #8fa88f;
  background: #eff5ed;
}

.mood-option span {
  font-size: 21px;
}

.mood-option small {
  color: #68756b;
  font-size: 10px;
}

.editor textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5eae3;
  border-radius: 9px;
  background: white;
  color: #526b57;
  font: inherit;
  font-size: 13px;
  resize: vertical;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 18px;
}

.actions button {
  padding: 9px 15px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.actions button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cancel-button {
  border: 1px solid #dfe7dd;
  background: white;
  color: #617c65;
}

.save-button {
  border: 1px solid #718d73;
  background: #718d73;
  color: white;
}
</style>
