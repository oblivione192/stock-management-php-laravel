import { createPinia } from 'pinia';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';;
import useProfileStore from '@/stores/profileStore';
import App from './App.vue';
import { clearAuthToken, getAuthToken, getJson } from './lib/api';
import { routes } from './router';

import '../css/app.css';

const pinia = createPinia();

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const profileStore = useProfileStore(pinia);
    const token = getAuthToken();
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const guestOnly = to.matched.some((record) => record.meta.guestOnly);

    if (!token) {
        profileStore.clearProfile();

        if (requiresAuth) {
            return { name: 'auth' };
        }

        return true;
    }

    if (!profileStore.isAuthenticated) {
        try {
            const profile = await getJson<{ name: string; email: string }>(
                '/inventory/profile',
            );

            profileStore.setProfile(profile.name, profile.email);
        } catch  {

            clearAuthToken();
            profileStore.clearProfile();

            return guestOnly ? true : { name: 'auth' };
        }
    }

    if (guestOnly) {
        return { name: 'home' };
    }

    return true;
});

createApp(App)
    .use(router)
    .use(pinia)
    .mount('#app');
