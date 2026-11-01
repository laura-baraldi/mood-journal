<script setup lang="ts">
const { logout } = useAuth();
const { t } = useI18n();
const config = useRuntimeConfig();

type MoodEntry = {
  id: number;
  mood: string;
  note: string | null;
  created_at: string;
  updated_at?: string;
};

const user = ref<{ name: string; email: string } | null>(null);
const moodEntries = ref<MoodEntry[]>([]);
const isLoading = ref(true);
const isSidebarOpen = ref(false);

const selectedMood = ref("happy");

async function loadMoodEntries() {
  try {
    const response = await $fetch<{ data: MoodEntry[] }>("/api/mood-entries", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });

    moodEntries.value = response.data;
  } catch (error) {
    console.error("Failed to load mood entries:", error);
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

    await loadMoodEntries();
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

      <DashboardHeader :name="user.name.split(' ')[0] ?? user.name" />

      <section class="content-grid">
        <div class="left-column">
          <DashboardMoodCheckIn
            v-model:selected-mood="selectedMood"
            @saved="loadMoodEntries"
          />
          <DashboardWeeklyOverview :entries="moodEntries" />
        </div>

        <div class="right-column">
          <DashboardMoodSummary :entries="moodEntries" />
          <DashboardInspirationPanel />
        </div>
      </section>

      <footer class="footer">{{ t("dashboard.footer") }} <span>♡</span></footer>
    </main>
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap");

:root {
  font-family: "DM Sans", sans-serif;
  color: #303b36;
  background: #f7f8f4;
  font-synthesis: none;
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
}

button,
textarea,
input {
  font: inherit;
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

.dashboard {
  min-height: 100vh;
  display: flex;
}

.main-content {
  width: min(1400px, 100%);
  margin: 0 auto;
  padding: 43px 48px 25px;
  min-width: 0;
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

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.7fr) minmax(230px, 0.85fr);
  gap: 22px;
  align-items: start;
}

.left-column,
.right-column {
  display: grid;
  gap: 20px;
}

.footer {
  text-align: center;
  padding: 28px 0 0;
  color: #a0aaa0;
  font-size: 10px;
}

.footer span {
  color: #91aa8e;
}

@media (max-width: 900px) {
  .menu-button {
    display: flex;
  }

  .main-content {
    padding: 30px 24px;
  }

  .content-grid {
    grid-template-columns: 1fr;
  }

  .right-column {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 600px) {
  .dashboard {
    display: block;
  }

  .main-content {
    padding: 20px 15px;
  }

  .right-column {
    grid-template-columns: 1fr;
  }
}
</style>
