<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { getJson } from '@/lib/api';
import type {MovementsResponse, PaginatedMovements} from '@/Objects/Responses/StockMovementResponses';  
import type { MovementFilters } from '@/Objects/Filters/MovementFilter'; 
import { createDefaultMovementFilters } from '@/Objects/Filters/MovementFilter'; 

const loading = ref(true);
const error = ref('');
const page = ref(1); 
const limit = ref(20); 
const data = ref<PaginatedMovements | null>(null); 
const filters = ref<MovementFilters>(createDefaultMovementFilters());
const buildQueryString = (): string => {
    const params = new URLSearchParams({
        page: String(page.value),
        limit: String(limit.value),
    });

    if (filters.value.product_name.trim()) {
        params.set('product_name', filters.value.product_name.trim());
    }

    if (filters.value.product_sku.trim()) {
        params.set('product_sku', filters.value.product_sku.trim());
    }

     if (filters.value.movement_type) {
        params.set('movement_type', filters.value.movement_type);
    }

    if (filters.value.created_after) {
        params.set('created_after', filters.value.created_after);
    }

    if (filters.value.created_before) {
        params.set('created_before', filters.value.created_before);
    }

    return params.toString();
}; 

const applyFilters = async (): Promise<void> => {  

      if (page.value !== 1) {
        page.value = 1;

        return;
      }

    await load();
};

const resetFilters = async (): Promise<void> => {
    filters.value = createDefaultMovementFilters();

    await applyFilters();
};  

const load = async (): Promise<void> => {
    loading.value = true;
    error.value = '';

    const queryString = buildQueryString();

    try {
        const response = await getJson<MovementsResponse>(
            `/inventory/stock-movements?${queryString}`,
        );
        data.value = response.movements;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unknown error.';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(page, load);
</script>

<template>
    <section class="space-y-4">
        <h2 class="text-2xl font-semibold">Stock Movements</h2>
        <form
            class="grid gap-3 rounded-lg border border-neutral-800 bg-neutral-900 p-4 sm:grid-cols-2 lg:grid-cols-3"
            @submit.prevent="applyFilters"
        >
            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Product Name</span>
                <input
                    v-model="filters.product_name"
                    type="text"
                    placeholder="Search by name"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Product SKU</span>
                <input
                    v-model="filters.product_sku"
                    type="text"
                    placeholder="Search by SKU"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Movement Type</span> 
                <select 
                v-model="filters.movement_type"
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                >
                    <option value="">All types</option>
                    <option value="stock_in">Stock In</option>
                    <option value="stock_out">Stock Out</option>
                    <option value="adjustment">Adjustment</option>
                </select>
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Created After</span>
                <input
                    v-model="filters.created_after"
                    type="date"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Created Before</span>
                <input
                    v-model="filters.created_before"
                    type="date"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <div
                class="flex items-center justify-end gap-2 sm:col-span-2 lg:col-span-3"
            >
                <button
                    type="button"
                    class="rounded-md border border-neutral-700 px-4 py-2 text-sm hover:bg-neutral-800"
                    @click="resetFilters"
                >
                    Reset
                </button>
                <button
                    type="submit"
                    class="rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-neutral-900 hover:bg-emerald-400"
                >
                    Apply Filters
                </button>
            </div>
        </form> 

        <p v-if="loading" class="text-neutral-300">Loading stock movements...</p>
        <p v-else-if="error" class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-red-200">
            {{ error }}
        </p>

        <template v-else-if="data">
            <div class="overflow-auto rounded-lg border border-neutral-800">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-900">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-right">Quantity</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="movement in data.data" :key="movement.id" class="border-t border-neutral-800">
                            <td class="px-4 py-3">{{ new Date(movement.moved_at).toLocaleString() }}</td>
                            <td class="px-4 py-3">{{ movement.product?.name ?? 'Unknown product' }}</td>
                            <td class="px-4 py-3">{{ movement.movement_type }}</td>
                            <td class="px-4 py-3 text-right">{{ movement.quantity }}</td>
                            <td class="px-4 py-3">{{ movement.reference ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between text-sm text-neutral-300">
                <p>Page {{ data.current_page }} of {{ data.last_page }} | {{ data.total }} total records</p>
                <div class="flex gap-2">
                    <button
                        class="rounded border border-neutral-700 px-3 py-1 disabled:opacity-40"
                        :disabled="page <= 1"
                        @click="page -= 1"
                    >
                        Previous
                    </button>
                    <button
                        class="rounded border border-neutral-700 px-3 py-1 disabled:opacity-40"
                        :disabled="page >= data.last_page"
                        @click="page += 1"
                    >
                        Next
                    </button>
                </div>
            </div>
        </template>
    </section>
</template>
