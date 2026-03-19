import {createPinia} from 'pinia';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import { getAuthToken } from './lib/api';
import { routes } from './router'; 
import '../css/app.css';

const router = createRouter({
    history: createWebHistory(),
    routes,
}); 

router.beforeEach((to) => {
    const isAuthenticated = Boolean(getAuthToken());
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const guestOnly = to.matched.some((record) => record.meta.guestOnly);

    if (requiresAuth && !isAuthenticated) {
        return { name: 'auth' };
    }

    if (guestOnly && isAuthenticated) {
        return { name: 'dashboard' };
    }

    return true;
});

const pinia = createPinia();

createApp(App).use(router).use(pinia).mount('#app');
