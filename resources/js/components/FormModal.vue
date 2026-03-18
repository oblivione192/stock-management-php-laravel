<script setup lang="ts">
import { onBeforeUnmount, onMounted } from 'vue';

type Props = {
	modelValue: boolean;
	title: string;
	description?: string;
	submitLabel?: string;
	cancelLabel?: string;
	submitting?: boolean;
	closeOnBackdrop?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
	description: '',
	submitLabel: 'Save',
	cancelLabel: 'Cancel',
	submitting: false,
	closeOnBackdrop: true,
});

const emit = defineEmits<{
	(e: 'update:modelValue', value: boolean): void;
	(e: 'submit'): void;
	(e: 'close'): void;
}>();

const close = (): void => {
	emit('update:modelValue', false);
	emit('close');
};

const onBackdropClick = (event: MouseEvent): void => {
	if (!props.closeOnBackdrop) {
		return;
	}

	if (event.target === event.currentTarget) {
		close();
	}
};

const onKeyDown = (event: KeyboardEvent): void => {
	if (!props.modelValue || event.key !== 'Escape') {
		return;
	}

	close();
};

onMounted(() => {
	window.addEventListener('keydown', onKeyDown);
});

onBeforeUnmount(() => {
	window.removeEventListener('keydown', onKeyDown);
});
</script>

<template>
	<Teleport to="body">
		<transition name="modal-fade">
			<div
				v-if="modelValue"
				class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-950/70 px-4 py-6"
				@click="onBackdropClick"
			>
				<div class="w-full max-w-2xl rounded-xl border border-neutral-700 bg-neutral-900 shadow-xl">
					<header class="border-b border-neutral-800 px-5 py-4">
						<h3 class="text-lg font-semibold text-neutral-100">{{ title }}</h3>
						<p v-if="description" class="mt-1 text-sm text-neutral-400">{{ description }}</p>
					</header>

					<form class="space-y-4 p-5" @submit.prevent="emit('submit')">
						<slot />

						<footer class="flex items-center justify-end gap-2 border-t border-neutral-800 pt-4">
							<slot name="actions">
								<button
									type="button"
									class="rounded-md border border-neutral-700 px-3 py-2 text-sm text-neutral-200 hover:bg-neutral-800"
									:disabled="submitting"
									@click="close"
								>
									{{ cancelLabel }}
								</button>
								<button
									type="submit"
									class="rounded-md bg-emerald-500 px-3 py-2 text-sm font-semibold text-neutral-900 hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-50"
									:disabled="submitting"
								>
									{{ submitLabel }}
								</button>
							</slot>
						</footer>
					</form>
				</div>
			</div>
		</transition>
	</Teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
	transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
	opacity: 0;
}
</style>
