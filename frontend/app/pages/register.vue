<script setup lang="ts">
const config = useRuntimeConfig();
const { t } = useI18n();
const localePath = useLocalePath();

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

    await navigateTo(localePath("/"));
  } catch (error: any) {
    if (error.data?.errors) {
      errorMessage.value = Object.values(error.data.errors).flat().join(" ");
    } else {
      errorMessage.value = error.data?.message || t("auth.registerFailed");
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <AuthShell
    :eyebrow="t('auth.registerEyebrow')"
    :title="t('auth.registerTitle')"
    :subtitle="t('auth.registerSubtitle')"
  >
    <form class="auth-form" @submit.prevent="register">
      <div class="field">
        <label for="name">{{ t("auth.name") }}</label>
        <input
          id="name"
          v-model="name"
          type="text"
          required
          autocomplete="name"
        />
      </div>

      <div class="field">
        <label for="email">{{ t("auth.email") }}</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
          autocomplete="email"
        />
      </div>

      <div class="field">
        <label for="password">{{ t("auth.password") }}</label>
        <input
          id="password"
          v-model="password"
          type="password"
          required
          minlength="8"
          autocomplete="new-password"
        />
        <small>{{ t("auth.passwordHint") }}</small>
      </div>

      <div class="field">
        <label for="password-confirmation">{{
          t("auth.confirmPassword")
        }}</label>
        <input
          id="password-confirmation"
          v-model="passwordConfirmation"
          type="password"
          required
          minlength="8"
          autocomplete="new-password"
        />
      </div>

      <p v-if="errorMessage" class="error-message" role="alert">
        {{ errorMessage }}
      </p>

      <button class="submit-button" type="submit" :disabled="isLoading">
        {{ isLoading ? t("auth.registering") : t("auth.register") }}
        <span v-if="!isLoading">→</span>
      </button>
    </form>

    <template #footer>
      {{ t("auth.haveAccount") }}
      <NuxtLink :to="localePath('/login')">{{ t("auth.logIn") }}</NuxtLink>
    </template>
  </AuthShell>
</template>

<style scoped>
.auth-form {
  display: grid;
  gap: 16px;
}

.field {
  display: grid;
  gap: 8px;
}

.field label {
  font-size: 12px;
  font-weight: 600;
}

.field input {
  width: 100%;
  border: 1px solid #e8ede6;
  border-radius: 10px;
  padding: 13px 14px;
  outline: none;
  color: #445047;
  font-size: 13px;
  background: #fcfdfa;
}

.field input:focus {
  border-color: #7b9b83;
  box-shadow: 0 0 0 3px #e9f0e8;
}

.field small {
  color: #99a59a;
  font-size: 11px;
}

.error-message {
  margin: 0;
  padding: 10px;
  border-radius: 8px;
  background: #fff0ef;
  color: #a33e38;
  font-size: 11px;
}

.submit-button {
  width: 100%;
  margin-top: 4px;
  padding: 14px;
  border: 0;
  border-radius: 10px;
  background: #718f76;
  color: white;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.submit-button:hover {
  background: #5f7e65;
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.submit-button span {
  margin-left: 7px;
}
</style>
