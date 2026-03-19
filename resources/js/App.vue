<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink, RouterView, useRoute } from 'vue-router';  

const route = useRoute();

const showHeader = computed(() => !route.meta.hideHeader);
const mainClassName = computed(() => {
    if (showHeader.value) {
        return 'mx-auto w-full max-w-6xl px-6 py-6';
    }

    return 'mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-6 py-10';
});

</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
        <header
            v-if="showHeader"
            class="border-b border-neutral-800 bg-neutral-900/70 backdrop-blur"
        >
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-300">Stock Management</p>
                    <h1 class="text-lg font-semibold">Frontend SPA</h1>
                </div>

                <nav class="flex flex-wrap items-center justify-end gap-3 text-sm">
                    <RouterLink to="/" class="rounded-md px-3 py-2 hover:bg-neutral-800">Home</RouterLink>
                    <RouterLink to="/dashboard" class="rounded-md px-3 py-2 hover:bg-neutral-800">Dashboard</RouterLink>
                    <RouterLink to="/categories" class="rounded-md px-3 py-2 hover:bg-neutral-800">Categories</RouterLink>
                    <RouterLink to="/products" class="rounded-md px-3 py-2 hover:bg-neutral-800">Products</RouterLink>
                    <RouterLink to="/stock-movements" class="rounded-md px-3 py-2 hover:bg-neutral-800">Stock Movements</RouterLink>
                </nav>
            </div>
        </header>

        <main :class="mainClassName">
            <RouterView />
        </main>
    </div>
</template>
