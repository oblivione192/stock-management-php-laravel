<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { getJson } from '@/lib/api';
import type {DashboardResponse} from '@/Objects/Responses/DashboardResponse';

const loading = ref(true);
const error = ref('');
const data = ref<DashboardResponse | null>(null);

const currency = (value: number): string =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MYR',
    }).format(value);

const load = async (): Promise<void> => {
    loading.value = true;
    error.value = '';

    try {
        data.value = await getJson<DashboardResponse>('/inventory/dashboard');
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unknown error.';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
</script>

<template>
    <section class="space-y-4">
        <h2 class="text-2xl font-semibold">Dashboard</h2>

        <p v-if="loading" class="text-neutral-300">Loading dashboard data...</p>
        <p v-else-if="error" class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-red-200">
            {{ error }}
        </p>

        <template v-else-if="data">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Products</p>
                    <p class="text-2xl font-semibold">{{ data.metrics.products }}</p>
                </article>
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Categories</p>
                    <p class="text-2xl font-semibold">{{ data.metrics.categories }}</p>
                </article>
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Suppliers</p>
                    <p class="text-2xl font-semibold">{{ data.metrics.suppliers }}</p>
                </article>
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Units On Hand</p>
                    <p class="text-2xl font-semibold">{{ data.metrics.totalUnits }}</p>
                </article>
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Low Stock</p>
                    <p class="text-2xl font-semibold text-amber-300">{{ data.metrics.lowStockProducts }}</p>
                </article>
                <article class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <p class="text-sm text-neutral-400">Stock Value</p>
                    <p class="text-2xl font-semibold">{{ currency(data.metrics.totalStockValue) }}</p>
                </article>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <section class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-neutral-300">Low-stock products</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="item in data.lowStockProducts" :key="item.id" class="rounded-md border border-neutral-800 p-3">
                            <p class="font-medium">{{ item.name }} <span class="font-mono text-xs text-neutral-400">({{ item.sku }})</span></p>
                            <p class="text-neutral-400">Current: {{ item.current_stock }} | Reorder: {{ item.reorder_level }}</p>
                        </li>
                        <li v-if="data.lowStockProducts.length === 0" class="text-neutral-400">No low-stock products.</li>
                    </ul>
                </section>

                <section class="rounded-lg border border-neutral-800 bg-neutral-900 p-4">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-neutral-300">Recent movements</h3>
                    <ul class="space-y-2 text-sm">
                        <li v-for="item in data.recentMovements" :key="item.id" class="rounded-md border border-neutral-800 p-3">
                            <p class="font-medium">{{ item.product?.name ?? 'Unknown product' }}</p>
                            <p class="text-neutral-400">
                                {{ item.movement_type }} | Qty: {{ item.quantity }} | {{ item.reference ?? 'No reference' }}
                            </p>
                        </li>
                        <li v-if="data.recentMovements.length === 0" class="text-neutral-400">No movement data.</li>
                    </ul>
                </section>
            </div>
        </template>
    </section>
</template>
