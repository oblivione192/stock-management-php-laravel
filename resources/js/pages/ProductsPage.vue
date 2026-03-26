<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import DeleteConfirmationModal from '@/components/DeleteConfirmationModal.vue';
import FormModal from '@/components/FormModal.vue';
import ProductForm from '@/components/ProductForm.vue';
import EventBus from '@/events/EventBus';
import { Events } from '@/events/Events';
import { getJson, postJson, putJson, deleteRequest } from '@/lib/api';
import { createDefaultProductFilters } from '@/Objects/Filters/ProductFilters';
import type { ProductFilters } from '@/Objects/Filters/ProductFilters';
import {
    createDefaultProductFormPayload,
    toProductPayload,
} from '@/Objects/Payloads/ProductPayloads';
import type {
    CreateProductPayload,
    ProductFormPayload,
} from '@/Objects/Payloads/ProductPayloads';
import type {
    CategoryOption,
    PaginatedProducts,
    ProductCategoriesResponse,
    ProductDeleteResult,
    ProductRow,
    ProductsResponse,
} from '@/Objects/Responses/ProductResponses';
import eventBus from '@/events/EventBus';

const loading = ref(true);
const error = ref('');
const creating = ref(false);
const createError = ref('');

const isEditModalOpen = ref(false);
const editError = ref('');
const editing = ref(false);
const isDeleteModalOpen = ref(false);
const deleting = ref(false);
const deleteError = ref('');

const isCreateModalOpen = ref(false);
const page = ref(1);
const limit = ref(20);
const data = ref<PaginatedProducts | null>(null);
const categories = ref<CategoryOption[]>([]);

const filters = ref<ProductFilters>(createDefaultProductFilters());
const newProduct = ref<ProductFormPayload>(createDefaultProductFormPayload());
const productForUpdate = ref<ProductFormPayload | null>(null);
const productForDelete = ref<ProductRow | null>(null);

const money = (value: string | number): string =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MYR',
    }).format(Number(value));

const buildQueryString = (): string => {
    const params = new URLSearchParams({
        page: String(page.value),
        limit: String(limit.value),
    });

    if (filters.value.name.trim()) {
        params.set('name', filters.value.name.trim());
    }

    if (filters.value.sku.trim()) {
        params.set('sku', filters.value.sku.trim());
    }

    if (filters.value.category_id.trim()) {
        params.set('category_id', filters.value.category_id.trim());
    }

    if (filters.value.supplier_id.trim()) {
        params.set('supplier_id', filters.value.supplier_id.trim());
    }

    if (filters.value.created_after) {
        params.set('created_after', filters.value.created_after);
    }

    if (filters.value.created_before) {
        params.set('created_before', filters.value.created_before);
    }

    return params.toString();
};

const loadCategories = async (): Promise<void> => {
    try {
        const response = await getJson<ProductCategoriesResponse>(
            '/inventory/category?limit=200',
        );
        categories.value = response.data;
    } catch {
        categories.value = [];
    }
};

const load = async (): Promise<void> => {
    loading.value = true;
    error.value = '';

    try {
        const queryString = buildQueryString();
        const response = await getJson<ProductsResponse>(
            `/inventory/products?${queryString}`,
        );

        data.value = response.products;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unknown error.';
    } finally {
        loading.value = false;
    }
};

const applyFilters = async (): Promise<void> => {
    if (page.value !== 1) {
        page.value = 1;

        return;
    }

    await load();
};

const resetFilters = async (): Promise<void> => {
    filters.value = createDefaultProductFilters();

    await applyFilters();
};

const openCreateModal = (): void => {
    createError.value = '';
    newProduct.value = createDefaultProductFormPayload();
    isCreateModalOpen.value = true;
};

const openEditModal = (existingProduct: ProductRow): void => {
    editError.value = '';
    const payload = {};
    Object.assign(payload, existingProduct);
    productForUpdate.value = payload as ProductFormPayload;
    isEditModalOpen.value = true;
};

const openDeleteModal = (product: ProductRow): void => {
    deleteError.value = '';
    productForDelete.value = product;
    isDeleteModalOpen.value = true;
};

const confirmDelete = async (): Promise<void> => {
    const product = productForDelete.value;

    if (!product) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await deleteRequest(`/inventory/products/${product.id}`);
        isDeleteModalOpen.value = false;

        if (data.value?.data.length === 1 && page.value > 1) {
            page.value -= 1;

            return;
        }

        const productLossInfo: ProductDeleteResult = {
            id: product.id,
            loss:{
                current_stock: product.current_stock,
                cost_price: product.cost_price,
            }
        };

        eventBus.emit(Events.PRODUCT_DELETED, productLossInfo);

        await load();
    } catch (err) {
        deleteError.value =
            err instanceof Error ? err.message : 'Unable to delete product.';
    } finally {
        deleting.value = false;
    }
};

const submitCreate = async (): Promise<void> => {
    creating.value = true;
    createError.value = '';

    try {
        const productResponse = await postJson<
            ProductRow,
            CreateProductPayload
        >('/inventory/products', toProductPayload(newProduct.value));

        isCreateModalOpen.value = false;

        if (page.value !== 1) {
            page.value = 1;

            return;
        }

        EventBus.emit(Events.PRODUCT_ADDED, productResponse);

        await load();
    } catch (err) {
        createError.value =
            err instanceof Error ? err.message : 'Unable to create product.';
    } finally {
        creating.value = false;
    }
};

const submitEdit = async (): Promise<void> => {
    const product = productForUpdate.value;

    if (!product?.id) {
        return;
    }

    editing.value = true;
    editError.value = '';

    try {
        await putJson<ProductRow, CreateProductPayload>(
            `/inventory/products/${product.id}`,
            toProductPayload(product),
        );

        isEditModalOpen.value = false;
        await load();
    } catch (err) {
        editError.value =
            err instanceof Error ? err.message : 'Unable to update product.';
    } finally {
        editing.value = false;
    }
};

onMounted(async () => {
    await Promise.all([loadCategories(), load()]);
});

watch(page, load);
watch(isDeleteModalOpen, (open) => {
    if (!open) {
        deleteError.value = '';
        productForDelete.value = null;
    }
});
</script>

<template>
    <section class="space-y-4">
        <header class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-semibold">Products</h2>
            <button
                class="rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-neutral-900 hover:bg-emerald-400"
                @click="openCreateModal"
            >
                New Product
            </button>
        </header>

        <form
            class="grid gap-3 rounded-lg border border-neutral-800 bg-neutral-900 p-4 sm:grid-cols-2 lg:grid-cols-3"
            @submit.prevent="applyFilters"
        >
            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Product Name</span>
                <input
                    v-model="filters.name"
                    type="text"
                    placeholder="Search by name"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">SKU</span>
                <input
                    v-model="filters.sku"
                    type="text"
                    placeholder="Search by SKU"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Category</span>
                <select
                    v-model="filters.category_id"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                >
                    <option value="">All categories</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="String(category.id)"
                    >
                        {{ category.name }}
                    </option>
                </select>
            </label>

            <label class="space-y-1 text-sm">
                <span class="text-neutral-300">Supplier ID</span>
                <input
                    v-model="filters.supplier_id"
                    type="number"
                    min="1"
                    placeholder="Optional"
                    class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm"
                />
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

        <p v-if="loading" class="text-neutral-300">Loading products...</p>
        <p
            v-else-if="error"
            class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-red-200"
        >
            {{ error }}
        </p>

        <template v-else-if="data">
            <div class="overflow-auto rounded-lg border border-neutral-800">
                <table class="min-w-full divide-y divide-neutral-800 text-sm">
                    <thead class="bg-neutral-900">
                        <tr>
                            <th class="px-4 py-3 text-left">SKU</th>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-right">Cost</th>
                            <th class="px-4 py-3 text-right">Price</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in data.data"
                            :key="product.id"
                            class="border-t border-neutral-800"
                        >
                            <td class="px-4 py-3 font-mono text-xs">
                                {{ product.sku }}
                            </td>
                            <td class="px-4 py-3">{{ product.name }}</td>
                            <td class="px-4 py-3">
                                {{ product.category?.name ?? 'Uncategorized' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ money(product.cost_price) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ money(product.selling_price) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ product.current_stock }}
                            </td>
                            <td
                                class="flex flex-col items-center gap-2 px-4 py-3 text-left sm:flex-row"
                            >
                                <button
                                    type="button"
                                    class="text-blue-500 hover:underline"
                                    @click="openEditModal(product)"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="text-red-500 hover:underline"
                                    @click="openDeleteModal(product)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex items-center justify-between text-sm text-neutral-300"
            >
                <p>
                    Page {{ data.current_page }} of {{ data.last_page }} |
                    {{ data.total }} total products
                </p>
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

        <FormModal
            v-model="isCreateModalOpen"
            title="Create Product"
            description="Add a product to your inventory catalog."
            submit-label="Create Product"
            :submitting="creating"
            @submit="submitCreate"
        >
            <ProductForm
                :model-value="newProduct"
                :categories="categories"
                :error="createError"
            />
        </FormModal>

        <FormModal
            v-model="isEditModalOpen"
            title="Edit Product"
            description="Update the product details."
            submit-label="Update Product"
            :submitting="editing"
            @submit="submitEdit"
        >
            <ProductForm
                v-if="productForUpdate"
                :model-value="productForUpdate"
                :categories="categories"
                :error="editError"
            />
        </FormModal>

        <DeleteConfirmationModal
            v-model="isDeleteModalOpen"
            :product-name="productForDelete?.name ?? ''"
            :submitting="deleting"
            :error="deleteError"
            :on-confirm="confirmDelete"
        />
    </section>
</template>
