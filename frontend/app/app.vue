<script setup lang="ts">
const config = useRuntimeConfig();

const status = ref("Testing...");
const result = ref("");

onMounted(async () => {
  try {
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    status.value = "CSRF cookie request successful";

    await $fetch("/api/user", {
      baseURL: config.public.apiBase,
      credentials: "include",
      headers: {
        Accept: "application/json",
      },
    });
  } catch (error: any) {
    if (error.statusCode === 401) {
      result.value = "Unauthenticated: expected, no user logged in yet.";
    } else {
      result.value = error.message;
    }
  }
});
</script>

<template>
  <NuxtPage />
</template>
