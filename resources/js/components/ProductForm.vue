<script setup lang="ts">
import { computed } from 'vue';
import type { ProductFormPayload } from '../Objects/Payloads/ProductPayloads';
import type { CategoryOption } from '../Objects/Responses/ProductResponses';

type Props = {
    modelValue: ProductFormPayload;
    categories: CategoryOption[];
    error?: string;
};

const props = withDefaults(defineProps<Props>(), {
    error: '',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ProductFormPayload): void;
}>();

const product = computed({
  get: () => props.modelValue,
  set: (value: ProductFormPayload) => emit('update:modelValue', value),
});



</script>

<template>
    <p v-if="props.error" class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-sm text-red-200">
        {{ props.error }}
    </p>

    <div class="grid gap-3 sm:grid-cols-2">
        <label class="space-y-1 text-sm sm:col-span-2">
            <span class="text-neutral-300">Product Name</span>
            <input
                v-model="product.name"
                type="text"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>

        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">SKU</span>
            <input
                v-model="product.sku"
                type="text"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>

        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Moved At</span>
            <input
                v-model="product.moved_at"
                type="date"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>

        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Category</span>
            <select
                v-model="product.category_id"
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
                <option value="">No category</option>
                <option v-for="category in categories" :key="category.id" :value="String(category.id)">
                    {{ category.name }}
                </option>
            </select>
        </label>


        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Unit</span> 
            <input
                v-model="product.unit"
                type="text"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>

       <label class="space-y-1 text-sm">
        <span class="text-neutral-300">Cost Price</span>

        <div class="flex items-center rounded-md border border-neutral-700 bg-neutral-950 overflow-hidden">
            <span class="shrink-0 border-r border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-400">
                RM
            </span>

            <input
                v-model="product.cost_price"
                type="number"
                min="0"
                step="0.01"
                required
                class="w-full bg-transparent px-3 py-2 text-neutral-100 outline-none"
            >
        </div>
    </label>

        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Selling Price</span>
            <div class="flex items-center rounded-md border border-neutral-700 bg-neutral-950 overflow-hidden">
                
                <span class="shrink-0 border-r border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-400">
                    RM
                </span>

                <input
                    v-model="product.selling_price"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    class="w-full bg-transparent px-3 py-2 text-neutral-100 outline-none"
                >
            </div>
        </label>


        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Reorder Level</span>
            <input
                v-model="product.reorder_level"
                type="number"
                min="0"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>

        <label class="space-y-1 text-sm">
            <span class="text-neutral-300">Current Stock</span>
            <input
                v-model="product.current_stock"
                type="number"
                min="0"
                required
                class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-neutral-100"
            >
        </label>
    </div>
</template>
