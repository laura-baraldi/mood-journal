<script setup lang="ts">
type MoodEntry = {
  id: number;
  mood: string;
  note: string | null;
  created_at: string;
  updated_at?: string;
};

const { logout } = useAuth();
const { t, locale } = useI18n();
const config = useRuntimeConfig();

const user = ref<{ name: string; email: string } | null>(null);
const entries = ref<MoodEntry[]>([]);
const isLoading = ref(true);
const isSidebarOpen = ref(false);
const errorMessage = ref("");

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

const groupedEntries = computed(() => {
  const groups = new Map<string, MoodEntry[]>();

  for (const entry of entries.value) {
    const date = new Date(entry.created_at);
    const key = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;

    if (!groups.has(key)) {
      groups.set(key, []);
    }

    groups.get(key)!.push(entry);
  }

  return Array.from(groups.entries()).map(([key, dayEntries]) => {
    const date = new Date(dayEntries[0]!.created_at);

    return {
      key,
      date,
      entries: dayEntries,
    };
  });
});

const latestEntry = computed(() => entries.value[0] ?? null);

const uniqueDays = computed(() => {
  const days = new Set(
    entries.value.map((entry) => {
      const date = new Date(entry.created_at);
      return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
    }),
  );

  return days.size;
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

async function loadEntries() {
  errorMessage.value = "";

  try {
    const response = await $fetch<{ data: MoodEntry[] }>("/api/mood-entries", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });

    entries.value = response.data;
  } catch (error) {
    console.error("Failed to load journal entries:", error);
    errorMessage.value = t("journal.loadError");
  }
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

    await loadEntries();
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

      <section class="stats-grid">
        <article class="stat-card">
          <span>{{ t("journal.totalEntries") }}</span>
          <strong>{{ entries.length }}</strong>
        </article>

        <article class="stat-card">
          <span>{{ t("journal.daysTracked") }}</span>
          <strong>{{ uniqueDays }}</strong>
        </article>

        <article class="stat-card">
          <span>{{ t("journal.latestMood") }}</span>
          <strong class="latest-mood">
            <span>{{
              latestEntry ? (moods[latestEntry.mood] ?? "—") : "—"
            }}</span>
            <span v-if="latestEntry">
              {{ t(`moods.${latestEntry.mood}`) }}
            </span>
          </strong>
        </article>
      </section>

      <section class="journal-section">
        <div class="section-heading">
          <div>
            <p class="eyebrow">{{ t("journal.historyEyebrow") }}</p>
            <h2>{{ t("journal.historyTitle") }}</h2>
          </div>

          <span class="entry-count">
            {{ entries.length }} {{ t("journal.entriesLabel") }}
          </span>
        </div>

        <div v-if="errorMessage" class="state-card error-state">
          <p>{{ errorMessage }}</p>
          <button @click="loadEntries">
            {{ t("journal.tryAgain") }}
          </button>
        </div>

        <div v-else-if="entries.length === 0" class="state-card empty-state">
          <span class="empty-icon">✳</span>
          <h3>{{ t("journal.emptyTitle") }}</h3>
          <p>{{ t("journal.emptyDescription") }}</p>
        </div>

        <div v-else class="timeline">
          <article
            v-for="group in groupedEntries"
            :key="group.key"
            class="day-group"
          >
            <h3 class="day-heading">{{ formatDay(group.date) }}</h3>

            <div class="day-entries">
              <article
                v-for="entry in group.entries"
                :key="entry.id"
                class="entry-card"
              >
                <div class="entry-top">
                  <div class="mood-icon">
                    {{ moods[entry.mood] ?? "—" }}
                  </div>

                  <div class="entry-info">
                    <strong>{{ t(`moods.${entry.mood}`) }}</strong>
                    <span>{{ formatTime(entry.created_at) }}</span>
                  </div>
                </div>

                <p v-if="entry.note" class="entry-note">
                  {{ entry.note }}
                </p>
                <p v-else class="no-note">
                  {{ t("journal.noNote") }}
                </p>
              </article>
            </div>
          </article>
        </div>
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
h2,
h3 {
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
  font-size: 17px;
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

  .journal-section {
    padding: 18px 14px;
  }

  .section-heading {
    align-items: flex-start;
  }

  .entry-card {
    padding: 14px;
  }
}
</style>
