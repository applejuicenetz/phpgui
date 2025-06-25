<script setup>
// Auth Layout für Login/Register Seiten ohne Sidebar/Header
</script>

<template>
  <div class="auth-layout">
    <div class="auth-container">
      <router-view />
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.auth-container {
  width: 100%;
  max-width: 500px;
}

/* Mobile Optimierung */
@media (max-width: 768px) {
  .auth-layout {
    padding: 1rem;
  }
  
  .auth-container {
    max-width: 100%;
  }
}
</style>