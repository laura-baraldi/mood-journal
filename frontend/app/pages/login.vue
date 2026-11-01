<script setup lang="ts">
const config = useRuntimeConfig();

const email = ref("");
const password = ref("");

const errorMessage = ref("");
const isLoading = ref(false);

async function login() {
  errorMessage.value = "";
  isLoading.value = true;

  try {
    // Get the CSRF cookie from Laravel Sanctum.
    await $fetch("/sanctum/csrf-cookie", {
      baseURL: config.public.apiBase,
      credentials: "include",
    });

    // Read the XSRF token stored in the browser cookie.
    const xsrfCookie = useCookie("XSRF-TOKEN");

    // Authenticate the user and start the Laravel session.
    await $fetch("/api/login", {
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
        email: email.value,
        password: password.value,
      },
    });

    await navigateTo("/");
  } catch (error: any) {
    if (error.data?.errors) {
      errorMessage.value = Object.values(error.data.errors).flat().join(" ");
    } else {
      errorMessage.value =
        error.data?.message || "Login failed. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <main>
    <h1>Welcome back</h1>

    <form @submit.prevent="login">
      <div>
        <label for="email">Email</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
          autocomplete="email"
        />
      </div>

      <div>
        <label for="password">Password</label>
        <input
          id="password"
          v-model="password"
          type="password"
          required
          autocomplete="current-password"
        />
      </div>

      <p v-if="errorMessage" role="alert">
        {{ errorMessage }}
      </p>

      <button type="submit" :disabled="isLoading">
        {{ isLoading ? "Signing in..." : "Login" }}
      </button>
    </form>
  </main>
</template>
