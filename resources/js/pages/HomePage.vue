<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import useProfileStore from '@/stores/profileStore'; 

const profileStore = useProfileStore();
const isAuthenticated = computed(() => profileStore.name!== ''); 

const router = useRouter();

const logOut = (): void => {
    profileStore.clearProfile();
    localStorage.removeItem('inventory_api_token');
    router.go(0);
}; 

</script>

<template>
    <section class="rounded-xl border border-neutral-800 bg-neutral-900 p-6">
        <h2 class="text-2xl font-semibold">Welcome to SPA Stock Management System</h2>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="/dashboard" class="rounded-md border border-neutral-700 px-4 py-2 text-sm">
                Open Dashboard
            </a>
            <a href="/products" class="rounded-md border border-neutral-700 px-4 py-2 text-sm">
                View Products
            </a>
            <a href="/categories" class="rounded-md border border-neutral-700 px-4 py-2 text-sm">
                View Categories
            </a>
            <a href="/stock-movements" class="rounded-md border border-neutral-700 px-4 py-2 text-sm">
                View Stock Movements
            </a>
        </div> 
        <button
            @click="logOut"
            v-if="isAuthenticated"
            class="mt-6 rounded-md border border-red-600 bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
        >
            Log Out
        </button>
    </section>
</template>
