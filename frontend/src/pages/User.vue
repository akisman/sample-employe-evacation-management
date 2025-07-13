<template>

  <!-- Global Top Bar -->
  <TopBar />

  <div class="p-8 max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        {{ userId ? 'Edit Employee' : 'Create New Employee' }}
      </h1>
      <button
          @click="goBack"
          class="p-2 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 cursor-pointer"
      >
        Back to Manager
      </button>
    </div>

    <form @submit.prevent="submitForm" class="space-y-4">
      <div>
        <label class="block mb-1">Name</label>
        <input
            type="text"
            v-model="form.name"
            required
            class="w-full border rounded p-2"
        />
      </div>

      <div>
        <label class="block mb-1">Employee Code</label>
        <input
            type="number"
            v-model="form.employee_code"
            required
            class="w-full border rounded p-2"
        />
      </div>

      <div>
        <label class="block mb-1">Email</label>
        <input
            type="email"
            v-model="form.email"
            required
            class="w-full border rounded p-2"
        />
      </div>

      <div>
        <label class="block mb-1">Role</label>
        <select
            v-model="form.role"
            required
            class="w-full border rounded p-2"
        >
          <option value="employee">Employee</option>
          <option value="manager">Manager</option>
        </select>
      </div>

      <div v-if="!userId">
        <label class="block mb-1">Password</label>
        <input
            type="password"
            v-model="form.password"
            required
            class="w-full border rounded p-2"
        />
      </div>

      <div v-if="userId">
        <label class="block mb-1">New Password (leave blank to keep current)</label>
        <input
            type="password"
            v-model="form.password"
            class="w-full border rounded p-2"
        />
      </div>

      <div class="flex justify-end space-x-4 pt-4">
        <button
            v-if="userId"
            @click.prevent="confirmDelete"
            type="button"
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 cursor-pointer"
        >
          Delete
        </button>
        <button
            type="submit"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer"
            :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Saving...' : 'Save' }}
        </button>
      </div>

      <div v-if="error" class="text-red-500 mt-4">{{ error }}</div>
      <div v-if="success" class="text-green-500 mt-4">{{ success }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import TopBar from "@/components/TopBar.vue";

const route = useRoute();
const router = useRouter();

const rawId = route.params.id;
const userId = ref(isFinite(rawId) ? Number(rawId) : null);
const isSubmitting = ref(false);
const error = ref('');
const success = ref('');

const form = ref({
  name: '',
  employee_code: null,
  email: '',
  role: 'employee',
  password: ''
});

// Load user data if editing
onMounted(async () => {
  if (userId.value) {
    try {
      const response = await axios.get(`/api/users/${userId.value}`);
      form.value = {
        name: response.data.name,
        employee_code: response.data.employee_code,
        email: response.data.email,
        role: response.data.role,
        password: ''
      };
    } catch (err) {
      error.value = 'Failed to load user data';
      console.error(err);
    }
  }
});

async function submitForm() {
  isSubmitting.value = true;
  error.value = '';
  success.value = '';

  try {
    if (userId.value) {
      // Update existing user
      await axios.put(`/api/users/${userId.value}`, form.value);
      success.value = 'User updated successfully';
      setTimeout(() => router.push('/manager'), 1000);
    } else {
      // Create new user
      await axios.post('/api/users', form.value);
      success.value = 'User created successfully';
      form.value = { name: '', employee_code: null, email: '', role: 'employee', password: '' };
      setTimeout(() => router.push('/manager'), 1000);
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred';
    console.error(err);
  } finally {
    isSubmitting.value = false;
  }
}

function confirmDelete() {
  if (confirm('Are you sure you want to delete this user?')) {
    deleteUser();
  }
}

async function deleteUser() {
  isSubmitting.value = true;
  try {
    await axios.delete(`/api/users/${userId.value}`);
    success.value = 'User deleted successfully';
    setTimeout(() => router.push('/manager'), 1000);
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete user';
    console.error(err);
  } finally {
    isSubmitting.value = false;
  }
}

function goBack() {
  router.push('/manager');
}
</script>