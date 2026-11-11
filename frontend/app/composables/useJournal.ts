export type MoodEntry = {
  id: number;
  mood: string;
  note: string | null;
  created_at: string;
  updated_at?: string;
};

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

export function useJournal() {
  const config = useRuntimeConfig();
  const { t } = useI18n();

  const entries = ref<MoodEntry[]>([]);
  const latestEntry = ref<MoodEntry | null>(null);

  const isEntriesLoading = ref(false);
  const isUpdating = ref(false);
  const isDeleting = ref(false);

  const errorMessage = ref("");
  const actionError = ref("");
  const actionSuccess = ref("");

  const editingEntry = ref<MoodEntry | null>(null);
  const editMood = ref("");
  const editNote = ref("");

  const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });

  const stats = ref({
    total_entries: 0,
    days_tracked: 0,
  });

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
        query: { page },
      });

      entries.value = response.data;
      meta.value = response.meta;
      stats.value = response.stats;

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
      editMood.value = "";
      editNote.value = "";

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

  return {
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
  };
}
