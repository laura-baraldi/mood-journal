<script setup lang="ts">
type MoodEntry = {
  id: number;
  mood: string;
  note: string | null;
  created_at: string;
  updated_at?: string;
};

// Describes the paginated response returned by the Laravel API.
type JournalResponse = {
  data: MoodEntry[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  stats: {
    total_entries: number;
    days_tracked: number;
  };
};

const { logout } = useAuth();
const { t } = useI18n();
const config = useRuntimeConfig();

const user = ref<{ name: string; email: string } | null>(null);
const entries = ref<MoodEntry[]>([]);
const latestEntry = ref<MoodEntry | null>(null);

const isLoading = ref(true);
const isEntriesLoading = ref(false);
const isSidebarOpen = ref(false);
const errorMessage = ref("");

const editingEntry = ref<MoodEntry | null>(null);
const editMood = ref("");
const editNote = ref("");
const isUpdating = ref(false);
const isDeleting = ref(false);
const actionError = ref("");
const actionSuccess = ref("");

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

// Pagination metadata is provided by the backend.
const meta = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

// Statistics cover all entries, not just the current page.
const stats = ref({
  total_entries: 0,
  days_tracked: 0,
});

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

// Fetches one page of journal entries from the Laravel API.
async function loadEntries(page = 1) {
  errorMessage.value = "";
  isEntriesLoading.value = true;

  try {
    const response = await $fetch<JournalResponse>("/api/mood-entries", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
      query: {
        page,
      },
    });

    // Update the displayed entries and pagination metadata.
    entries.value = response.data;
    meta.value = response.meta;

    // Keep statistics independent of the currently displayed page.
    stats.value = response.stats;

    // The latest entry is always found on the first page.
    if (response.meta.current_page === 1) {
      latestEntry.value = response.data[0] ?? null;
    }
  } catch (error) {
    console.error("Failed to load journal entries:", error);
    errorMessage.value = t("journal.loadError");
  } finally {
    isEntriesLoading.value = false;
  }
}

// Prevents navigation outside the available pages.
async function goToPage(page: number) {
  if (page < 1 || page > meta.value.last_page || isEntriesLoading.value) {
    return;
  }

  await loadEntries(page);
}

function startEditing(entry: MoodEntry) {
  editingEntry.value = entry;
  editMood.value = entry.mood;
  editNote.value = entry.note ?? "";
  actionError.value = "";
  actionSuccess.value = "";
}

function cancelEditing() {
  editingEntry.value = null;
  editMood.value = "";
  editNote.value = "";
  actionError.value = "";
}

async function updateEntry() {
  if (!editingEntry.value || isUpdating.value) return;

  isUpdating.value = true;
  actionError.value = "";
  actionSuccess.value = "";

  try {
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    const xsrfCookie = useCookie("XSRF-TOKEN");

    await $fetch(`/api/mood-entries/${editingEntry.value.id}`, {
      baseURL: config.public.apiBase,
      method: "PATCH",
      credentials: "include",
      headers: {
        Accept: "application/json",
        "X-XSRF-TOKEN": xsrfCookie.value
          ? decodeURIComponent(xsrfCookie.value)
          : "",
      },
      body: {
        mood: editMood.value,
        note: editNote.value.trim() || null,
      },
    });

    editingEntry.value = null;
    actionSuccess.value = t("journal.updated");

    await loadEntries(meta.value.current_page);
  } catch (error: any) {
    actionError.value = error.data?.message || t("journal.updateError");
  } finally {
    isUpdating.value = false;
  }
}

async function deleteEntry(id: number) {
  if (isDeleting.value) return;

  if (!window.confirm(t("journal.deleteConfirm"))) return;

  isDeleting.value = true;
  actionError.value = "";
  actionSuccess.value = "";

  try {
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    const xsrfCookie = useCookie("XSRF-TOKEN");

    await $fetch(`/api/mood-entries/${id}`, {
      baseURL: config.public.apiBase,
      method: "DELETE",
      credentials: "include",
      headers: {
        Accept: "application/json",
        "X-XSRF-TOKEN": xsrfCookie.value
          ? decodeURIComponent(xsrfCookie.value)
          : "",
      },
    });

    if (editingEntry.value?.id === id) {
      cancelEditing();
    }

    const nextPage =
      entries.value.length === 1 && meta.value.current_page > 1
        ? meta.value.current_page - 1
        : meta.value.current_page;

    actionSuccess.value = t("journal.deleted");

    await loadEntries(nextPage);
  } catch (error: any) {
    actionError.value = error.data?.message || t("journal.deleteError");
  } finally {
    isDeleting.value = false;
  }
}

// Load the authenticated user and the first journal page on mount.
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

          <JournalTimeline v-else :entries="entries" :moods="moods" />

          <!-- Pagination controls are shown only when multiple pages exist. -->
          <nav
            v-if="meta.last_page > 1"
            class="pagination"
            :aria-label="t('journal.pagination')"
          >
            <button
              class="pagination-button"
              :disabled="meta.current_page === 1 || isEntriesLoading"
              @click="goToPage(meta.current_page - 1)"
            >
              {{ t("journal.previous") }}
            </button>

            <span class="page-indicator">
              {{ t("journal.page") }} {{ meta.current_page }}
              {{ t("journal.of") }} {{ meta.last_page }}
            </span>

            <button
              class="pagination-button"
              :disabled="
                meta.current_page === meta.last_page || isEntriesLoading
              "
              @click="goToPage(meta.current_page + 1)"
            >
              {{ t("journal.next") }}
            </button>
          </nav>
        </template>
      </section>

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

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-top: 30px;
  padding-top: 22px;
  border-top: 1px solid #edf0eb;
}

.pagination-button {
  padding: 9px 15px;
  border: 1px solid #dfe7dd;
  border-radius: 8px;
  background: white;
  color: #617c65;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition:
    background 0.2s,
    border-color 0.2s;
}

.pagination-button:hover:not(:disabled) {
  background: #f3f7f1;
  border-color: #a9bba7;
}

.pagination-button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-indicator {
  color: #89968b;
  font-size: 12px;
  white-space: nowrap;
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

  .journal-section {
    padding: 18px 14px;
  }

  .section-heading {
    align-items: flex-start;
  }

  .pagination {
    gap: 10px;
  }

  .pagination-button {
    padding: 9px 11px;
  }
}
</style>
