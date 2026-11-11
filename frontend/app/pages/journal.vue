<script setup lang="ts">
const { logout } = useAuth();
const { t, locale, setLocale } = useI18n();
const config = useRuntimeConfig();

const user = ref<{ name: string; email: string } | null>(null);
const isLoading = ref(true);
const isSidebarOpen = ref(false);

const deleteTargetId = ref<number | null>(null);

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

const {
  entries,
  latestEntry,
  isEntriesLoading,
  isUpdating,
  isDeleting,
  errorMessage,
  actionError,
  actionSuccess,
  editingEntry,
  editMood,
  editNote,
  meta,
  stats,
  loadEntries,
  goToPage,
  startEditing,
  cancelEditing,
  updateEntry,
  deleteEntry,
} = useJournal();

function requestDelete(id: number) {
  deleteTargetId.value = id;
}

function cancelDelete() {
  if (isDeleting.value) return;

  deleteTargetId.value = null;
}

async function confirmDelete() {
  if (deleteTargetId.value === null || isDeleting.value) return;

  await deleteEntry(deleteTargetId.value);

  deleteTargetId.value = null;
}

onMounted(async () => {
  try {
    user.value = await $fetch("/api/user", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });

    await loadEntries(1);
  } catch {
    await navigateTo("/login");
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div v-if="isLoading" class="loading-screen">
    <span class="loading-dot"></span>
    {{ t("dashboard.loading") }}
  </div>

  <div v-else-if="user" class="dashboard">
    <DashboardAppSidebar
      :user="user"
      :logout="logout"
      :is-open="isSidebarOpen"
      @close="isSidebarOpen = false"
    />

    <main class="main-content">
      <button
        class="menu-button"
        :aria-label="t('navigation.openMenu')"
        @click="isSidebarOpen = true"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <header class="page-header">
        <div>
          <p class="eyebrow">{{ t("journal.eyebrow") }}</p>
          <h1>{{ t("journal.title") }}</h1>
          <p class="subtitle">{{ t("journal.subtitle") }}</p>
        </div>

        <div class="language-switch" aria-label="Choose language">
          <button
            type="button"
            :class="{ active: locale === 'en' }"
            :aria-pressed="locale === 'en'"
            @click="setLocale('en')"
          >
            EN
          </button>

          <button
            type="button"
            :class="{ active: locale === 'sv' }"
            :aria-pressed="locale === 'sv'"
            @click="setLocale('sv')"
          >
            SV
          </button>
        </div>
      </header>

      <JournalStats
        :total-entries="stats.total_entries"
        :days-tracked="stats.days_tracked"
        :latest-mood-emoji="
          latestEntry ? (moods[latestEntry.mood] ?? '—') : '—'
        "
        :latest-mood-label="latestEntry ? t(`moods.${latestEntry.mood}`) : ''"
      />

      <section class="journal-section">
        <div class="section-heading">
          <div>
            <p class="eyebrow">{{ t("journal.historyEyebrow") }}</p>
            <h2>{{ t("journal.historyTitle") }}</h2>
          </div>

          <span class="entry-count">
            {{ stats.total_entries }} {{ t("journal.entriesLabel") }}
          </span>
        </div>

        <div v-if="actionError" class="feedback error-feedback">
          {{ actionError }}
        </div>

        <div v-if="actionSuccess" class="feedback success-feedback">
          {{ actionSuccess }}
        </div>

        <div v-if="errorMessage" class="state-card error-state">
          <p>{{ errorMessage }}</p>
          <button @click="loadEntries(meta.current_page)">
            {{ t("journal.tryAgain") }}
          </button>
        </div>

        <template v-else>
          <div v-if="isEntriesLoading" class="loading-message">
            {{ t("dashboard.loading") }}
          </div>

          <JournalTimeline
            v-else
            :entries="entries"
            :moods="moods"
            :editing-entry="editingEntry"
            :edit-mood="editMood"
            :edit-note="editNote"
            :is-updating="isUpdating"
            @edit="startEditing"
            @delete="requestDelete"
            @update:mood="editMood = $event"
            @update:note="editNote = $event"
            @save-edit="updateEntry"
            @cancel-edit="cancelEditing"
          />

          <JournalPagination
            :current-page="meta.current_page"
            :last-page="meta.last_page"
            :is-loading="isEntriesLoading"
            @change="goToPage"
          />
        </template>
      </section>

      <JournalDeleteDialog
        v-if="deleteTargetId !== null"
        :is-deleting="isDeleting"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />

      <footer class="footer">{{ t("dashboard.footer") }} <span>♡</span></footer>
    </main>
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  display: flex;
  background: #f7f8f4;
}

.main-content {
  width: min(1200px, 100%);
  margin: 0 auto;
  padding: 42px 48px 24px;
  min-width: 0;
}

.loading-screen {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  color: #69776c;
}

.loading-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #7b9b83;
}

.menu-button {
  display: none;
  width: 42px;
  height: 42px;
  margin-bottom: 20px;
  padding: 10px;
  border: 1px solid #e7ebe4;
  border-radius: 10px;
  background: white;
  cursor: pointer;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
}

.menu-button span {
  display: block;
  width: 19px;
  height: 2px;
  background: #617c65;
  border-radius: 2px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.eyebrow {
  margin: 0 0 9px;
  color: #91a092;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.5px;
}

h1,
h2 {
  font-family: "Manrope", sans-serif;
  color: #303b36;
}

h1 {
  margin: 0;
  font-size: clamp(28px, 4vw, 36px);
  letter-spacing: -1px;
}

.subtitle {
  margin: 10px 0 0;
  color: #909b91;
  font-size: 14px;
}

.language-switch {
  display: flex;
  gap: 3px;
  padding: 4px;
  border: 1px solid #e7ebe4;
  border-radius: 30px;
  background: white;
  flex-shrink: 0;
}

.language-switch button {
  border: 0;
  border-radius: 20px;
  padding: 7px 10px;
  background: transparent;
  color: #89948a;
  font-size: 10px;
  font-weight: 700;
  cursor: pointer;
}

.language-switch button.active {
  background: #edf3eb;
  color: #56745e;
}

.journal-section {
  padding: 26px;
  background: white;
  border: 1px solid #e9ede7;
  border-radius: 16px;
}

.section-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 28px;
}

.section-heading h2 {
  margin: 0;
  font-size: 21px;
}

.entry-count {
  color: #89968b;
  font-size: 11px;
}

.state-card {
  padding: 45px 20px;
  text-align: center;
  border-radius: 12px;
  background: #fbfcfa;
}

.state-card p {
  color: #929d93;
  font-size: 13px;
  line-height: 1.6;
}

.error-state button {
  margin-top: 10px;
  padding: 10px 16px;
  border: 0;
  border-radius: 8px;
  background: #718f76;
  color: white;
  cursor: pointer;
}

.loading-message {
  padding: 35px 20px;
  text-align: center;
  color: #909b91;
  font-size: 13px;
}

.feedback {
  margin-bottom: 16px;
  padding: 12px 15px;
  border-radius: 9px;
  font-size: 13px;
}

.error-feedback {
  color: #9c4545;
  background: #fff2f0;
}

.success-feedback {
  color: #52765a;
  background: #f0f7ef;
}

.footer {
  padding: 25px 0 0;
  color: #a0aaa0;
  font-size: 10px;
  text-align: center;
}

.footer span {
  color: #91aa8e;
}

@media (max-width: 900px) {
  .menu-button {
    display: flex;
  }

  .main-content {
    padding: 28px 24px;
  }
}

@media (max-width: 600px) {
  .main-content {
    padding: 20px 15px;
  }

  .page-header {
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .journal-section {
    padding: 18px 14px;
  }

  .section-heading {
    align-items: flex-start;
  }
}
</style>
