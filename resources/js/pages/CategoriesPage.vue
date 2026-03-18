<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { getJson } from '../lib/api';
import {
    createDefaultCategoryFilters
    
} from '../Objects/Filters/CategoryFilters';
import type {CategoryFilters} from '../Objects/Filters/CategoryFilters';
import type {CategoriesResponse, CategoryRow} from '../Objects/Responses/CategoryResponses';

const loading = ref(true);
const error = ref('');
const categories = ref<CategoryRow[]>([]);
const filters = ref<CategoryFilters>(createDefaultCategoryFilters());

const buildQueryString = (): string => {
    const params = new URLSearchParams({
        limit: '200',
    });

    if (filters.value.name.trim()) {
        params.set('name', filters.value.name.trim());
    }

    if (filters.value.description.trim()) {
        params.set('description', filters.value.description.trim());
    }

    return params.toString();
};

const load = async (): Promise<void> => {
    loading.value = true;
    error.value = '';

    try {
        const queryString = buildQueryString();
        const response = await getJson<CategoriesResponse>(
            `/inventory/category?${queryString}`,
        );

        categories.value = response.data;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unable to load categories.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = async (): Promise<void> => {
    filters.value = createDefaultCategoryFilters();

    await load();
};

onMounted(load);
</script>

<template>
    <section class="space-y-4">
        <header class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Categories</h2>
            <p class="text-sm text-neutral-300">{{ categories.length }} shown</p>
        </header>

        <form class="grid gap-3 rounded-lg border border-neutral-800 bg-neutral-900 p-4 sm:grid-cols-2" @submit.prevent="load">
            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Category Name</span>
                <input
                    v-model="filters.name"
                    type="text"
                    placeholder="Search by category name"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                >
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Description</span>
                <input
                    v-model="filters.description"
                    type="text"
                    placeholder="Search by description"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                >
            </label>

            <div class="sm:col-span-2 flex items-center justify-end gap-2">
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

        <p v-if="loading" class="text-neutral-300">Loading categories...</p>
        <p v-else-if="error" class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-red-200">
            {{ error }}
        </p>

        <template v-else>
            <div class="overflow-auto rounded-lg border border-neutral-800">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-900">
                        <tr>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories" :key="category.id" class="border-t border-neutral-800">
                            <td class="px-4 py-3 font-mono text-xs">{{ category.id }}</td>
                            <td class="px-4 py-3">{{ category.name }}</td>
                            <td class="px-4 py-3">{{ category.description }}</td>
                            <td class="px-4 py-3">{{ new Date(category.created_at).toLocaleDateString() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-if="categories.length === 0" class="text-sm text-neutral-400">
                No categories found.
            </p>
        </template>
    </section>
</template>
