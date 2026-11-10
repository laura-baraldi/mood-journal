<script setup lang="ts">
defineProps<{
  isDeleting: boolean;
}>();

const emit = defineEmits<{
  confirm: [];
  cancel: [];
}>();

const { t } = useI18n();
</script>

<template>
  <Teleport to="body">
    <Transition name="dialog">
      <div class="dialog-overlay" @click.self="!isDeleting && emit('cancel')">
        <section
          class="dialog-card"
          role="alertdialog"
          aria-modal="true"
          :aria-label="t('journal.delete')"
        >
          <div class="dialog-icon">
            <span>✕</span>
          </div>

          <h2>{{ t("journal.delete") }}?</h2>

          <p>{{ t("journal.deleteConfirm") }}</p>

          <div class="dialog-actions">
            <button
              type="button"
              class="cancel-button"
              :disabled="isDeleting"
              @click="emit('cancel')"
            >
              {{ t("journal.cancel") }}
            </button>

            <button
              type="button"
              class="delete-button"
              :disabled="isDeleting"
              @click="emit('confirm')"
            >
              {{ isDeleting ? t("journal.saving") : t("journal.delete") }}
            </button>
          </div>
        </section>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.dialog-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: rgb(35 45 38 / 40%);
  backdrop-filter: blur(3px);
}

.dialog-card {
  width: min(420px, 100%);
  padding: 30px;
  border: 1px solid #e9ede7;
  border-radius: 18px;
  background: white;
  box-shadow: 0 20px 60px rgb(40 55 43 / 16%);
  text-align: center;
}

.dialog-icon {
  width: 48px;
  height: 48px;
  display: grid;
  place-items: center;
  margin: 0 auto 17px;
  border-radius: 50%;
  background: #fff1ef;
  color: #bd6860;
  font-size: 20px;
  font-weight: 600;
}

.dialog-card h2 {
  margin: 0;
  color: #303b36;
  font-family: "Manrope", sans-serif;
  font-size: 20px;
}

.dialog-card p {
  margin: 12px 0 25px;
  color: #879187;
  font-size: 13px;
  line-height: 1.7;
}

.dialog-actions {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.dialog-actions button {
  min-width: 110px;
  padding: 11px 18px;
  border-radius: 9px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
}

.dialog-actions button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cancel-button {
  border: 1px solid #dfe7dd;
  background: white;
  color: #617c65;
}

.cancel-button:hover:not(:disabled) {
  background: #f5f8f3;
}

.delete-button {
  border: 1px solid #b96860;
  background: #b96860;
  color: white;
}

.delete-button:hover:not(:disabled) {
  background: #a95750;
}

.dialog-enter-active,
.dialog-leave-active {
  transition: opacity 0.2s ease;
}

.dialog-enter-active .dialog-card,
.dialog-leave-active .dialog-card {
  transition: transform 0.2s ease;
}

.dialog-enter-from,
.dialog-leave-to {
  opacity: 0;
}

.dialog-enter-from .dialog-card,
.dialog-leave-to .dialog-card {
  transform: translateY(10px) scale(0.98);
}

@media (max-width: 480px) {
  .dialog-card {
    padding: 25px 20px;
  }

  .dialog-actions button {
    flex: 1;
    min-width: 0;
  }
}
</style>
