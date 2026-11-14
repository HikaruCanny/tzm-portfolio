<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <div class="grid min-h-screen lg:grid-cols-2">
        <Head title="Log in" />

        <section class="hidden flex-col justify-between bg-slate-950 px-12 py-10 text-white lg:flex">
            <Link href="/" class="text-sm font-semibold tracking-wide text-indigo-300">TZM.dev</Link>
            <div class="max-w-md space-y-4">
                <h1 class="text-4xl font-extrabold tracking-tight">Admin sign in</h1>
                <p class="text-slate-300">
                    This door is for invited admins. The public portfolio stays on the landing page.
                </p>
            </div>
            <p class="text-sm text-slate-500">Laravel, Vue, and Inertia.</p>
        </section>

        <section class="flex items-center justify-center bg-slate-50 px-6 py-16 dark:bg-slate-950">
            <div class="w-full max-w-md space-y-8">
                <div class="lg:hidden">
                    <Link href="/" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">TZM.dev</Link>
                    <h1 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Admin sign in</h1>
                </div>

                <div v-if="status" class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-slate-600 dark:text-slate-300">Remember me</span>
                        </label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Log in
                    </button>
                </form>

                <div class="border-t border-slate-200 pt-5 text-sm dark:border-slate-800">
                    <p class="text-slate-500">Invited admin?</p>
                    <Link :href="route('register')" class="mt-2 inline-flex font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                        Register with invite code
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
