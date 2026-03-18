<script setup lang="ts">
import FormModal from '@/components/FormModal.vue';

type Props = {
    modelValue: boolean;
    productName?: string;
    submitting?: boolean;
    error?: string;
    onConfirm?: () => void | Promise<void>;
};

const props = withDefaults(defineProps<Props>(), {
    productName: '',
    submitting: false,
    error: '',
    onConfirm: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const close = (): void => {
    if (props.submitting) {
        return;
    }

    emit('update:modelValue', false);
};

const submitConfirm = async (): Promise<void> => {
    if (props.submitting || !props.onConfirm) {
        return;
    }

    await props.onConfirm();
};
</script>

<template>
    <FormModal
        :model-value="modelValue"
        title="Delete Product"
        description="Please confirm before deleting this product."
        submit-label="Delete Product"
        cancel-label="Keep Product"
        :submitting="submitting"
        :close-on-backdrop="!submitting"
        @update:model-value="emit('update:modelValue', $event)"
        @submit="submitConfirm"
    >
        <div class="space-y-3">
            <p class="rounded-md border border-red-500/40 bg-red-950/30 p-3 text-sm text-red-200">
                You are about to permanently delete
                <span class="font-semibold text-red-100">{{ productName || 'this product' }}</span>.
                This action cannot be undone.
            </p>

            <p
                v-if="error"
                class="rounded-md border border-red-500/40 bg-red-950/20 p-3 text-sm text-red-200"
            >
                {{ error }}
            </p>
        </div>

        <template #actions>
            <button
                type="button"
                class="rounded-md border border-neutral-700 px-3 py-2 text-sm text-neutral-200 hover:bg-neutral-800 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="submitting"
                @click="close"
            >
                Keep Product
            </button>
            <button
                type="submit"
                class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="submitting"
            >
                Delete Product
            </button>
        </template>
    </FormModal>
</template>
