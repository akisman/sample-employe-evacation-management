<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
    <img src="/logo.png" alt="Logo" class="w-24 mb-8" />

    <form
        class="w-full max-w-sm bg-white p-8 rounded-lg shadow-lg space-y-6"
        @submit.prevent="submit"
    >
      <h1 class="text-3xl font-extrabold text-center text-gray-900">Sign in</h1>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            id="email"
            v-model="email"
            type="email"
            required
            placeholder="you@example.com"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
            id="password"
            v-model="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
        />
      </div>

      <button
          type="submit"
          class="w-full py-3 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold transition"
      >
        Login
      </button>

      <p v-if="error" class="text-red-600 text-sm text-center mt-2">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import {ref} from 'vue';
import {useRouter} from 'vue-router';
import auth from '@/stores/auth';

const email = ref('');
const password = ref('');
const error = ref('');
const router = useRouter();

async function submit() {
  error.value = '';
  try {
    const role = await auth.login(email.value, password.value);

    if (role === 'manager') {
      router.push('/manager');
    } else {
      router.push('/employee');
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Login failed';
  }
}
</script>
