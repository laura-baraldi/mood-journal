<script setup lang="ts">
const props = defineProps<{
  selectedMood: string;
}>();

const emit = defineEmits<{
  "update:selectedMood": [value: string];
}>();

const note = ref("");
const saved = ref(false);

const moods = [
  { id: "sad", emoji: "😢", label: "Sad" },
  { id: "lonely", emoji: "😔", label: "Lonely" },
  { id: "anxious", emoji: "😰", label: "Anxious" },
  { id: "frustrated", emoji: "😤", label: "Frustrated" },
  { id: "angry", emoji: "😡", label: "Angry" },
  { id: "tired", emoji: "😴", label: "Tired" },
  { id: "neutral", emoji: "😐", label: "Neutral" },
  { id: "calm", emoji: "😌", label: "Calm" },
  { id: "happy", emoji: "😊", label: "Happy" },
  { id: "excited", emoji: "🤩", label: "Excited" },
];

const currentMood = computed(() =>
  moods.find((mood) => mood.id === props.selectedMood),
);

function selectMood(id: string) {
  emit("update:selectedMood", id);
  saved.value = false;
}

function saveMood() {
  saved.value = true;
}
</script>

<template>
  <section class="card mood-card">
    <div class="section-heading">
      <div>
        <p class="eyebrow">DAILY CHECK-IN</p>
        <h2>How are you feeling?</h2>
        <p class="muted">
          There are no wrong answers. Just be honest with yourself.
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
        <span>{{ mood.label }}</span>
      </button>
    </div>

    <div class="note-section">
      <label for="note">Want to write something about it?</label>
      <textarea
        id="note"
        v-model="note"
        placeholder="What's on your mind today?..."
        rows="4"
        @input="saved = false"
      ></textarea>
    </div>

    <div v-if="saved" class="success-message">
      ✓ Your check-in has been recorded in this demo.
    </div>

    <button class="save-button" @click="saveMood">
      Save today's mood <span>→</span>
    </button>
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
  margin: 30px 0;
}

.mood-option {
  min-width: 0;
  min-height: 103px;
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

.save-button {
  width: 100%;
  border: 0;
  border-radius: 10px;
  padding: 14px;
  margin-top: 19px;
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
