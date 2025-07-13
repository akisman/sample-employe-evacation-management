<template>
  <header class="bg-white shadow mb-6">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <!-- Left: Logo -->
      <div class="flex items-center space-x-2">
        <img src="/logo.png" alt="Logo" class="h-16 w-auto" />
      </div>

      <!-- Right: Logout -->
      <button
          @click="logout"
          class="px-4 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600 cursor-pointer"
      >
        Logout
      </button>
    </div>
  </header>
</template>

<script setup>
import auth from "@/stores/auth.js";
import { useRouter } from 'vue-router';

const router = useRouter();

async function logout() {
  await auth.logout();
  router.push('/login');
}
</script>