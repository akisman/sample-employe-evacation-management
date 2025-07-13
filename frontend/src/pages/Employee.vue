<template>
  <!-- Global Top Bar -->
  <TopBar />

  <div class="p-8">
    <h1 class="text-2xl font-bold mb-6">Welcome, {{ user?.name }}</h1>

    <!-- Top Actions -->
    <div class="mb-6">
      <button
          @click="showForm = true"
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded cursor-pointer"
      >
        Request Vacation
      </button>
    </div>

    <!-- Vacation List -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b">
        <h2 class="text-lg font-semibold">My Vacation Requests</h2>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="vac in vacations" :key="vac.id">
          <td class="px-6 py-4">{{ vac.startDate }}</td>
          <td class="px-6 py-4">{{ vac.endDate }}</td>
          <td class="px-6 py-4">{{ vac.reason }}</td>
          <td class="px-6 py-4 capitalize">{{ vac.status }}</td>
        </tr>
        </tbody>
      </table>
      <div v-if="vacations.length === 0" class="p-4 text-center text-gray-500">
        No vacation requests yet.
      </div>
      <div v-if="error" class="p-4 text-red-600 text-center">{{ error }}</div>
    </div>
  </div>

  <!-- Vacation Request Modal -->
  <div
      v-if="showForm"
      class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50"
      style="background-color: rgba(0, 0, 0, 0.25)"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
      <h2 class="text-xl font-bold mb-4">Request Vacation</h2>
      <form @submit.prevent="submitVacationRequest" class="space-y-4">
        <div>
          <label class="block mb-1">Start Date</label>
          <input
              type="date"
              v-model="startDate"
              required
              class="w-full border rounded p-2"
          />
        </div>
        <div>
          <label class="block mb-1">End Date</label>
          <input
              type="date"
              v-model="endDate"
              required
              class="w-full border rounded p-2"
          />
        </div>
        <div>
          <label class="block mb-1">Reason</label>
          <textarea
              v-model="reason"
              class="w-full border rounded p-2"
              rows="3"
              required
          ></textarea>
        </div>
        <div class="flex justify-between items-center">
          <button
              type="submit"
              class="bg-green-600 text-white px-4 py-2 rounded cursor-pointer"
              :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Submitting...' : 'Submit' }}
          </button>
          <button
              type="button"
              @click="closeForm"
              class="text-gray-600 hover:text-black cursor-pointer"
          >
            Cancel
          </button>
        </div>
        <p v-if="formError" class="text-red-600">{{ formError }}</p>
        <p v-if="formSuccess" class="text-green-600">{{ formSuccess }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import auth from '@/stores/auth';
import TopBar from '@/components/TopBar.vue';

const { user } = auth;
const vacations = ref([]);
const error = ref('');
const showForm = ref(false);
const startDate = ref('');
const endDate = ref('');
const reason = ref('');
const formError = ref('');
const formSuccess = ref('');
const isSubmitting = ref(false);

async function loadVacations() {
  try {
    const res = await axios.get('/api/vacations');
    vacations.value = res.data;
  } catch (e) {
    error.value = 'Failed to load vacations.';
  }
}

async function submitVacationRequest() {
  if (isSubmitting.value) return;

  isSubmitting.value = true;
  formError.value = '';
  formSuccess.value = '';

  try {
    await axios.post('/api/vacations', {
      start_date: startDate.value,
      end_date: endDate.value,
      reason: reason.value
    });

    formSuccess.value = 'Vacation request submitted!';
    startDate.value = '';
    endDate.value = '';
    reason.value = '';
    await loadVacations();
    closeForm();
  } catch (e) {
    formError.value = e.response?.data?.message || 'Submission failed.';
  } finally {
    isSubmitting.value = false;
  }
}

function closeForm() {
  showForm.value = false;
  formError.value = '';
  formSuccess.value = '';
}

onMounted(loadVacations);
</script>
