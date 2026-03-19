<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { postJson, setAuthToken } from '@/lib/api';
import useProfileStore from '@/stores/profileStore';

type AuthMode = 'login' | 'register';

interface AuthUser {
    id: number;
    name: string;
    email: string;
}

interface LoginPayload {
    name: string;
    email: string;
    password: string;
}

interface RegisterPayload {
    name: string;
    email: string;
    password: string;
}

interface LoginResponse {
    message: string;
    token: string;
    user: AuthUser;
}

interface RegisterResponse {
    message: string;
    user: AuthUser;
}

const mode = ref<AuthMode>('login');
const submitting = ref(false);
const error = ref('');
const success = ref('');
const router = useRouter();
const profileStore = useProfileStore();

const loginForm = ref<LoginPayload>({
    name: '',
    email: '',
    password: '',
});

const registerForm = ref({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
});

const activeName = computed({
    get: (): string =>
        mode.value === 'login' ? loginForm.value.name : registerForm.value.name,
    set: (value: string): void => {
        if (mode.value === 'login') {
            loginForm.value.name = value;

            return;
        }

        registerForm.value.name = value;
    },
});

const activeEmail = computed({
    get: (): string =>
        mode.value === 'login' ? loginForm.value.email : registerForm.value.email,
    set: (value: string): void => {
        if (mode.value === 'login') {
            loginForm.value.email = value;

            return;
        }

        registerForm.value.email = value;
    },
});

const activePassword = computed({
    get: (): string =>
        mode.value === 'login'
            ? loginForm.value.password
            : registerForm.value.password,
    set: (value: string): void => {
        if (mode.value === 'login') {
            loginForm.value.password = value;

            return;
        }

        registerForm.value.password = value;
    },
});

const setMode = (nextMode: AuthMode): void => {
    if (mode.value === nextMode) {
        return;
    }

    mode.value = nextMode;
    error.value = '';
    success.value = '';
};

const submitLogin = async (): Promise<void> => {
    submitting.value = true;
    error.value = '';
    success.value = '';

    try {
        const payload: LoginPayload = {
            name: loginForm.value.name.trim(),
            email: loginForm.value.email.trim(),
            password: loginForm.value.password,
        };

        const response = await postJson<LoginResponse, LoginPayload>(
            '/inventory/auth/login',
            payload,
        );

        setAuthToken(response.token);
        profileStore.setProfile(response.user.name, response.user.email);
        success.value = '';
        loginForm.value.password = '';
        await router.replace({ name: 'dashboard' });
    } catch (err) {
        error.value =
            err instanceof Error ? err.message : 'Unable to complete login.';
    } finally {
        submitting.value = false;
    }
};

const submitRegister = async (): Promise<void> => {
    if (registerForm.value.password !== registerForm.value.confirmPassword) {
        error.value = 'Password and confirm password must match.';

        return;
    }

    submitting.value = true;
    error.value = '';
    success.value = '';

    try {
        const payload: RegisterPayload = {
            name: registerForm.value.name.trim(),
            email: registerForm.value.email.trim(),
            password: registerForm.value.password,
        };

        const response = await postJson<RegisterResponse, RegisterPayload>(
            '/inventory/auth/register',
            payload,
        );

        success.value = `${response.message}. You can now log in.`;
        mode.value = 'login';
        loginForm.value.name = payload.name;
        loginForm.value.email = payload.email;
        loginForm.value.password = '';
        registerForm.value.password = '';
        registerForm.value.confirmPassword = '';
    } catch (err) {
        error.value =
            err instanceof Error ? err.message : 'Unable to complete registration.';
    } finally {
        submitting.value = false;
    }
};

const submitActiveForm = async (): Promise<void> => {
    if (mode.value === 'login') {
        await submitLogin();

        return;
    }

    await submitRegister();
};
</script>

<template>
    <section
        class="relative w-full max-w-md overflow-hidden rounded-3xl border border-neutral-800 bg-neutral-950/90 p-6 shadow-2xl shadow-black/30 sm:p-8"
    >
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.16),transparent_45%),radial-gradient(circle_at_80%_85%,rgba(245,158,11,0.14),transparent_35%)]"
        />

        <div class="relative rounded-2xl border border-neutral-800 bg-neutral-900/85 p-5 animate-in fade-in slide-in-from-bottom-4 duration-500 sm:p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-300">
                Inventory Access
            </p>
            <h1 class="mt-3 text-3xl font-semibold leading-tight text-white">
                {{ mode === 'login' ? 'Sign in to continue' : 'Create your account' }}
            </h1>
            <p class="mt-2 text-sm text-neutral-300">
                {{
                    mode === 'login'
                        ? 'Use your account to access the stock management workspace.'
                        : 'Register a new account for the stock management workspace.'
                }}
            </p>

            <div class="mb-4 mt-6 grid grid-cols-2 gap-2 rounded-lg bg-neutral-950 p-1 text-sm">
                <button
                    type="button"
                    class="rounded-md px-3 py-2 font-medium transition"
                    :class="
                        mode === 'login'
                            ? 'bg-emerald-500 text-neutral-950'
                            : 'text-neutral-300 hover:bg-neutral-800'
                    "
                    @click="setMode('login')"
                >
                    Login
                </button>
                <button
                    type="button"
                    class="rounded-md px-3 py-2 font-medium transition"
                    :class="
                        mode === 'register'
                            ? 'bg-amber-400 text-neutral-950'
                            : 'text-neutral-300 hover:bg-neutral-800'
                    "
                    @click="setMode('register')"
                >
                    Register
                </button>
            </div>

            <form class="space-y-3" @submit.prevent="submitActiveForm">
                <label class="block space-y-1 text-sm">
                    <span class="text-neutral-300">Name</span>
                    <input
                        v-model="activeName"
                        type="text"
                        autocomplete="name"
                        placeholder="John Doe"
                        class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                        required
                    />
                </label>

                <label class="block space-y-1 text-sm">
                    <span class="text-neutral-300">Email</span>
                    <input
                        v-model="activeEmail"
                        type="email"
                        autocomplete="email"
                        placeholder="john@example.com"
                        class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                        required
                    />
                </label>

                <label class="block space-y-1 text-sm">
                    <span class="text-neutral-300">Password</span>
                    <input
                        v-model="activePassword"
                        type="password"
                        :autocomplete="
                            mode === 'login' ? 'current-password' : 'new-password'
                        "
                        placeholder="Minimum 6 characters"
                        class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                        required
                    />
                </label>

                <label
                    v-if="mode === 'register'"
                    class="block space-y-1 text-sm"
                >
                    <span class="text-neutral-300">Confirm Password</span>
                    <input
                        v-model="registerForm.confirmPassword"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Repeat your password"
                        class="w-full rounded-md border border-neutral-700 bg-neutral-950 px-3 py-2"
                        required
                    />
                </label>

                <button
                    type="submit"
                    class="mt-1 w-full rounded-md px-4 py-2 text-sm font-semibold text-neutral-950 transition disabled:cursor-not-allowed disabled:opacity-70"
                    :class="
                        mode === 'login'
                            ? 'bg-emerald-500 hover:bg-emerald-400'
                            : 'bg-amber-400 hover:bg-amber-300'
                    "
                    :disabled="submitting"
                >
                    <span v-if="submitting">Please wait...</span>
                    <span v-else-if="mode === 'login'">Sign In</span>
                    <span v-else>Create Account</span>
                </button>
            </form>

            <p
                v-if="error"
                class="mt-4 rounded-md border border-red-500/40 bg-red-950/30 p-3 text-sm text-red-200"
            >
                {{ error }}
            </p>
            <p
                v-if="success"
                class="mt-4 rounded-md border border-emerald-500/30 bg-emerald-950/30 p-3 text-sm text-emerald-200"
            >
                {{ success }}
            </p>
            <p class="mt-4 text-xs text-neutral-400">
                Note: this backend currently requires name, email, and password
                for login validation.
            </p>
        </div>
    </section>
</template>
