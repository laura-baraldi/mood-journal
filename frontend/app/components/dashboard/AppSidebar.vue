<script setup lang="ts">
defineProps<{
  user: {
    name: string;
    email: string;
  };
  logout: () => void;
  isOpen: boolean;
}>();

const emit = defineEmits<{
  close: [];
}>();

const { t } = useI18n();
</script>

<template>
  <div v-if="isOpen" class="sidebar-overlay" @click="emit('close')"></div>

  <aside class="sidebar" :class="{ 'sidebar-open': isOpen }">
    <div class="brand">
      <div class="brand-icon">✳</div>
      <span>{{ t("app.name") }}</span>

      <button
        class="close-button"
        :aria-label="t('navigation.closeMenu')"
        @click="emit('close')"
      >
        ×
      </button>
    </div>

    <div class="nav-label">{{ t("navigation.menu") }}</div>

    <nav class="navigation">
      <a href="#" class="nav-item active" @click="emit('close')">
        <span>◫</span>
        {{ t("navigation.dashboard") }}
      </a>

      <a href="#" class="nav-item" @click="emit('close')">
        <span>▤</span>
        {{ t("navigation.journal") }}
      </a>

      <a href="#" class="nav-item" @click="emit('close')">
        <span>▥</span>
        {{ t("navigation.insights") }}
      </a>
    </nav>

    <div class="sidebar-bottom">
      <div class="profile">
        <div class="avatar">
          {{ user.name.charAt(0).toUpperCase() }}
        </div>

        <div class="profile-info">
          <strong>{{ user.name }}</strong>
          <span>{{ user.email }}</span>
        </div>
      </div>

      <button class="logout-button" @click="logout">
        ↗ {{ t("navigation.logout") }}
      </button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 245px;
  min-height: 100vh;
  flex-shrink: 0;
  background: #fff;
  border-right: 1px solid #e9ede7;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  z-index: 20;
}

.brand {
  display: flex;
  align-items: center;
  gap: 11px;
  font-family: "Manrope", sans-serif;
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 60px;
}

.brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: #e9f0e8;
  color: #68856d;
  font-size: 24px;
}

.nav-label {
  margin: 0 0 14px 12px;
  font-size: 10px;
  letter-spacing: 1.5px;
  font-weight: 700;
  color: #99a59a;
}

.navigation {
  display: grid;
  gap: 7px;
}

.nav-item {
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 13px;
  padding: 13px 14px;
  border-radius: 10px;
  color: #7e8a80;
  font-size: 13px;
}

.nav-item span {
  font-size: 18px;
}

.nav-item.active {
  color: #56745e;
  background: #edf3eb;
  font-weight: 700;
}

.sidebar-bottom {
  margin-top: auto;
}

.profile {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 15px 0;
  border-top: 1px solid #edf0eb;
}

.avatar {
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: #e8eee5;
  color: #617c65;
  font-weight: 700;
}

.profile-info {
  min-width: 0;
  display: grid;
  gap: 3px;
}

.profile-info strong {
  font-size: 12px;
}

.profile-info span {
  font-size: 10px;
  color: #9aa49a;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-button {
  width: 100%;
  padding: 11px;
  border: 1px solid #e8ece6;
  border-radius: 9px;
  background: white;
  color: #7b867d;
  font-size: 12px;
  text-align: left;
  cursor: pointer;
}

.close-button {
  display: none;
  margin-left: auto;
  border: 0;
  background: transparent;
  color: #778278;
  font-size: 27px;
  cursor: pointer;
}

.sidebar-overlay {
  display: none;
}

@media (max-width: 900px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 270px;
    min-height: 100vh;
    transform: translateX(-100%);
    transition: transform 0.25s ease;
    box-shadow: none;
  }

  .sidebar.sidebar-open {
    transform: translateX(0);
    box-shadow: 8px 0 30px rgba(35, 50, 38, 0.12);
  }

  .close-button {
    display: block;
  }

  .sidebar-overlay {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(30, 40, 32, 0.3);
    z-index: 19;
  }
}
</style>
