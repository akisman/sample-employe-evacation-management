import { ref } from 'vue';
import axios from 'axios';

const user = ref(null);
let routerInstance = null;

export function setRouter(router) {
    routerInstance = router;
}

async function fetchUser() {
    try {
        const res = await axios.get('/api/me');
        user.value = res.data;
        return user.value;
    } catch (error) {
        user.value = null;
        return null;
    }
}

async function login(email, password) {
    const res = await axios.post('/api/login', { email, password });
    user.value = { id: res.data.id, role: res.data.role, name: res.data.name };
    return res.data.role;
}


async function logout() {
    try {
        await axios.post('/api/logout');
        user.value = null;
        if (routerInstance) {
            routerInstance.push('/login');
        } else {
            window.location.href = '/login'; // Fallback
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

export default {
    user,
    setRouter,
    fetchUser,
    login,
    logout
};
