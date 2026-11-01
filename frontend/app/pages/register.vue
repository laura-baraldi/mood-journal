<script setup lang="ts">
const config = useRuntimeConfig();

const name = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref("");

const errorMessage = ref("");
const isLoading = ref(false);

async function register() {
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

    // Register the user and start the Laravel session.
    await $fetch("/api/register", {
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
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      },
    });

    await navigateTo("/");
  } catch (error: any) {
    if (error.data?.errors) {
      errorMessage.value = Object.values(error.data.errors).flat().join(" ");
    } else {
      errorMessage.value =
        error.data?.message || "Registration failed. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <main>
    <h1>Create your account</h1>

    <form @submit.prevent="register">
      <div>
        <label for="name">Name</label>
        <input
          id="name"
          v-model="name"
          type="text"
          required
          autocomplete="name"
        />
      </div>

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
          minlength="8"
          autocomplete="new-password"
        />
      </div>

      <div>
        <label for="password-confirmation">Confirm password</label>
        <input
          id="password-confirmation"
          v-model="passwordConfirmation"
          type="password"
          required
          minlength="8"
          autocomplete="new-password"
        />
      </div>

      <p v-if="errorMessage" role="alert">{{ errorMessage }}</p>

      <button type="submit" :disabled="isLoading">
        {{ isLoading ? "Creating account..." : "Register" }}
      </button>
    </form>
  </main>
</template>
