<template>

  <!-- Global Top Bar -->
  <TopBar />

  <!-- Users Tab Content -->
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-6">Welcome, Manager!</h1>

    <!-- Tabs Navigation -->
    <div class="flex border-b mb-6">
      <button
          @click="activeTab = 'users'"
          :class="[
          'px-4 py-2 font-medium cursor-pointer',
          activeTab === 'users'
            ? 'border-b-2 border-blue-500 text-blue-600'
            : 'text-gray-500 hover:text-gray-700'
        ]"
      >
        Employees
      </button>
      <button
          @click="activeTab = 'vacations'"
          :class="[
            'px-4 py-2 font-medium flex items-center gap-1 cursor-pointer',
            activeTab === 'vacations'
              ? 'border-b-2 border-blue-500 text-blue-600'
              : 'text-gray-500 hover:text-gray-700'
          ]"
      >
        <span class="relative inline-flex items-center">
          Vacations
          <span
              :class="[
                'ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold leading-none rounded-full',
                pendingCount === 0
                  ? 'text-green-800 bg-green-200'
                  : 'text-red-800 bg-red-200 animate-pulse'
              ]"
          >
            {{ pendingCount }}
          </span>
        </span>
      </button>
    </div>

    <!-- Users Tab Content -->
    <div v-if="activeTab === 'users'" class="bg-white rounded-lg shadow">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Employee List</h2>
        <div class="space-x-2">
          <router-link
              to="/users/new"
              class="p-2 bg-green-100 text-green-600 rounded hover:bg-green-200"
          >
            Add Employee
          </router-link>
          <button
              @click="loadUsers"
              class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200"
          >
            Refresh
          </button>
        </div>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="user in users" :key="user.id">
          <td class="px-6 py-4 whitespace-nowrap">{{ user.employee_code }}</td>
          <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
          <td class="px-6 py-4 whitespace-nowrap capitalize">{{ user.role }}</td>
          <td class="px-6 py-4 whitespace-nowrap">
            <router-link
                :to="`/users/${user.id}`"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
            >
              Edit
            </router-link>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if="usersLoading" class="p-4 text-center text-gray-500">Loading employees...</div>
      <div v-if="usersError" class="p-4 text-center text-red-500">{{ usersError }}</div>
    </div>

    <!-- Vacations Tab Content -->
    <div v-if="activeTab === 'vacations'" class="bg-white rounded-lg shadow">
      <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">Pending Vacation Requests</h2>
        <button
            @click="loadPendingVacations"
            class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200"
        >
          Refresh
        </button>
      </div>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="vacation in pendingVacations" :key="vacation.id">
          <td class="px-6 py-4 whitespace-nowrap">{{ getUserName(vacation.userId) }}</td>
          <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(vacation.startDate) }}</td>
          <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(vacation.endDate) }}</td>
          <td class="px-6 py-4">{{ vacation.reason }}</td>
          <td class="px-6 py-4">{{ vacation.status }}</td>
          <td class="px-6 py-4 whitespace-nowrap space-x-2">
            <button
                @click="approveVacation(vacation.id)"
                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
            >
              Approve
            </button>
            <button
                @click="declineVacation(vacation.id)"
                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
            >
              Decline
            </button>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if="vacationsLoading" class="p-4 text-center text-gray-500">Loading vacation requests...</div>
      <div v-if="vacationsError" class="p-4 text-center text-red-500">{{ vacationsError }}</div>
      <div v-if="pendingVacations.length === 0 && !vacationsLoading" class="p-4 text-center text-gray-500">
        No pending vacation requests
      </div>
    </div>

  </div>



</template>

<script setup>
import TopBar from '@/components/TopBar.vue';
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import auth from '@/stores/auth';
import axios from 'axios';

const router = useRouter();
const activeTab = ref('users');

// Users data
const users = ref([]);
const usersLoading = ref(false);
const usersError = ref('');

// Vacations data
const pendingVacations = ref([]);
const pendingCount = computed(() =>
    pendingVacations.value.filter(v => v.status === 'pending').length
);
const vacationsLoading = ref(false);
const vacationsError = ref('');

// async function logout() {
//   await auth.logout();
//   router.push('/login');
// }

// User management functions
async function loadUsers() {
  usersLoading.value = true;
  usersError.value = '';
  try {
    const response = await axios.get('/api/users');
    users.value = response.data;
  } catch (error) {
    usersError.value = 'Failed to load employees';
    console.error('Error loading users:', error);
  } finally {
    usersLoading.value = false;
  }
}



// Vacation management functions
async function loadPendingVacations() {
  vacationsLoading.value = true;
  vacationsError.value = '';
  try {
    const response = await axios.get('/api/vacations/pending');
    pendingVacations.value = response.data;
  } catch (error) {
    vacationsError.value = 'Failed to load vacation requests';
    console.error('Error loading vacations:', error);
  } finally {
    vacationsLoading.value = false;
  }
}

function getUserName(userId) {
  const user = users.value.find(u => u.id === userId);
  return user ? user.name : 'Unknown';
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

async function approveVacation(vacationId) {
  try {
    await axios.put(`/api/vacations/${vacationId}/approve`);
    await loadPendingVacations();
  } catch (error) {
    console.error('Error approving vacation:', error);
    alert('Failed to approve vacation');
  }
}

async function declineVacation(vacationId) {
  try {
    await axios.put(`/api/vacations/${vacationId}/decline`);
    await loadPendingVacations();
  } catch (error) {
    console.error('Error rejecting vacation:', error);
    alert('Failed to reject vacation');
  }
}

// Load initial data
onMounted(() => {
  loadUsers();
  loadPendingVacations();
});
</script>

<style scoped>
/* Add any custom styles here */
</style>