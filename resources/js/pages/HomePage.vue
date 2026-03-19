<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import useProfileStore from '@/stores/profileStore'; 

const profileStore = useProfileStore();


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
       
    </section>
</template>
