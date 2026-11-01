<script setup lang="ts">
const { logout } = useAuth();

const user = ref<{ name: string; email: string } | null>(null);
const isLoading = ref(true);
const errorMessage = ref("");

const config = useRuntimeConfig();

onMounted(async () => {
  try {
    user.value = await $fetch("/api/user", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });
  } catch {
    await navigateTo("/login");
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <main>
    <h1>Mood Journal</h1>

    <p v-if="isLoading">Loading...</p>

    <div v-else-if="user">
      <p>Welcome, {{ user.name }}!</p>
      <p>{{ user.email }}</p>

      <button @click="logout">Logout</button>
    </div>
  </main>
</template>
