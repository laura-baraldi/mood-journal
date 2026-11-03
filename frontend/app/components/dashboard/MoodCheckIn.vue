<script setup lang="ts">
import { onBeforeUnmount } from "vue";

const props = defineProps<{
  selectedMood: string;
}>();

const emit = defineEmits<{
  "update:selectedMood": [value: string];
  saved: [];
}>();

const { t } = useI18n();
const config = useRuntimeConfig();

const note = ref("");
const saved = ref(false);
const isSaving = ref(false);
const errorMessage = ref("");

let successTimeout: ReturnType<typeof setTimeout> | undefined;

function clearSuccessTimeout() {
  if (successTimeout) {
    clearTimeout(successTimeout);
    successTimeout = undefined;
  }
}

onBeforeUnmount(() => {
  clearSuccessTimeout();
});

const moods = [
  { id: "sad", emoji: "😢" },
  { id: "lonely", emoji: "😔" },
  { id: "anxious", emoji: "😰" },
  { id: "frustrated", emoji: "😤" },
  { id: "angry", emoji: "😡" },
  { id: "tired", emoji: "😴" },
  { id: "neutral", emoji: "😐" },
  { id: "calm", emoji: "😌" },
  { id: "happy", emoji: "😊" },
  { id: "excited", emoji: "🤩" },
];

function selectMood(id: string) {
  clearSuccessTimeout();
  emit("update:selectedMood", id);
  saved.value = false;
  errorMessage.value = "";
}

async function saveMood() {
  clearSuccessTimeout();
  saved.value = false;
  errorMessage.value = "";
  isSaving.value = true;

  try {
    // Get the CSRF cookie from Laravel Sanctum.
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    // Read the XSRF token stored in the browser cookie.
    const xsrfCookie = useCookie("XSRF-TOKEN");

    // Save the mood entry for the authenticated user.
    await $fetch("/api/mood-entries", {
      baseURL: config.public.apiBase,
      method: "POST",
      credentials: "include",
      headers: {
        Accept: "application/json",
        "X-XSRF-TOKEN": xsrfCookie.value
          ? decodeURIComponent(xsrfCookie.value)
          : "",
      },
      body: {
        mood: props.selectedMood,
        note: note.value.trim() || null,
      },
    });

    saved.value = true;
    note.value = "";

    successTimeout = setTimeout(() => {
      saved.value = false;
      successTimeout = undefined;
    }, 3000);

    // Notify the parent that the mood was saved successfully.
    emit("saved");
  } catch (error: any) {
    if (error.data?.errors) {
      errorMessage.value = Object.values(error.data.errors).flat().join(" ");
    } else {
      errorMessage.value =
        error.data?.message || "Unable to save your mood. Please try again.";
    }
  } finally {
    isSaving.value = false;
  }
}
</script>

<template>
  <section class="card mood-card">
    <div class="section-heading">
      <div>
        <p class="eyebrow">{{ t("dashboard.checkIn") }}</p>
        <h2>{{ t("dashboard.question") }}</h2>
        <p class="muted">
          {{ t("dashboard.description") }}
        </p>
      </div>
      <span class="sparkle">✳</span>
    </div>

    <div class="mood-options">
      <button
        v-for="mood in moods"
        :key="mood.id"
        class="mood-option"
        :class="{ selected: selectedMood === mood.id }"
        @click="selectMood(mood.id)"
      >
        <span class="mood-emoji">{{ mood.emoji }}</span>
        <span>{{ t(`moods.${mood.id}`) }}</span>
      </button>
    </div>

    <div class="note-section">
      <label for="note">{{ t("dashboard.noteLabel") }}</label>
      <textarea
        id="note"
        v-model="note"
        :placeholder="t('dashboard.notePlaceholder')"
        rows="2"
        @input="
          saved = false;
          errorMessage = '';
        "
      ></textarea>
    </div>

    <div v-if="saved" class="success-message">✓ {{ t("dashboard.saved") }}</div>

    <div v-if="errorMessage" class="error-message" role="alert">
      {{ errorMessage }}
    </div>

    <button class="save-button" :disabled="isSaving" @click="saveMood">
      {{ isSaving ? "Saving..." : t("dashboard.save") }}
      <span>→</span>
    </button>
  </section>
</template>

<style scoped>
.card {
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
  padding: 20px 22px;
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
  letter-spacing: -0.5px;
  margin: 0 0 7px;
}

.muted {
  color: #909b91;
  font-size: 13px;
  margin: 0;
}

.sparkle {
  color: #9ab39a;
  font-size: 27px;
}

.mood-options {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 10px;
  margin: 18px 0;
}

.mood-option {
  min-width: 0;
  min-height: 82px;
  border: 1px solid #edf0eb;
  background: #fbfcfa;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 11px;
  color: #778278;
  font-size: 11px;
  transition: 0.2s;
  cursor: pointer;
}

.mood-option:hover {
  border-color: #b8cbb7;
  background: #f5f8f3;
}

.mood-option.selected {
  border: 1.5px solid #83a486;
  background: #eff5ed;
  color: #56765d;
  font-weight: 700;
}

.mood-emoji {
  font-size: 29px;
}

.note-section {
  display: grid;
  gap: 10px;
}

.note-section label {
  font-size: 12px;
  font-weight: 600;
}

.note-section textarea {
  resize: vertical;
  border: 1px solid #e8ede6;
  border-radius: 10px;
  padding: 14px;
  outline: none;
  color: #445047;
  font-size: 12px;
  background: #fcfdfa;
  font-family: inherit;
}

.note-section textarea:focus {
  border-color: #91ad93;
}

.note-section textarea::placeholder {
  color: #b0b9b0;
}

.success-message {
  margin-top: 14px;
  padding: 10px;
  background: #eff6ed;
  color: #5c7c61;
  border-radius: 8px;
  font-size: 11px;
}

.error-message {
  margin-top: 14px;
  padding: 10px;
  background: #fff0ef;
  color: #a33e38;
  border-radius: 8px;
  font-size: 11px;
}

.save-button {
  width: 100%;
  border: 0;
  border-radius: 10px;
  padding: 14px;
  margin-top: 14px;
  background: #718f76;
  color: white;
  font-size: 12px;
  font-weight: 700;
  transition: 0.2s;
  cursor: pointer;
}

.save-button:hover {
  background: #5f7e65;
}

.save-button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.save-button span {
  margin-left: 7px;
}

@media (max-width: 600px) {
  .card {
    padding: 19px;
  }

  .mood-options {
    gap: 6px;
  }

  .mood-option {
    min-height: 85px;
    font-size: 9px;
  }

  .mood-emoji {
    font-size: 24px;
  }
}
</style>
