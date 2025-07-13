import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/pages/Login.vue';
import Manager from '@/pages/Manager.vue';
import Employee from '@/pages/Employee.vue';
import User from '@/pages/User.vue';
import auth from '@/stores/auth';

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/manager', component: Manager, meta: { role: 'manager' } },
    { path: '/employee', component: Employee, meta: { role: 'employee' } },
    { path: '/users/:id(\\d+)', component: User, meta: { role: 'manager' } },
    { path: '/users/new', component: User, meta: { role: 'manager' } }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    try {
        // Only fetch user if not already loaded
        if (!auth.user.value && to.meta.role) {
            await auth.fetchUser();
        }

        const user = auth.user.value;

        // Your existing guard logic
        if (!user && to.meta.role) {
            return next('/login');
        }
        if (user && to.meta.role && user.role !== to.meta.role) {
            return next('/login');
        }
        if (user && to.path === '/login') {
            return next(`/${user.role}`);
        }

        next();
    } catch (error) {
        next('/login');
    }
});

export default router;
