<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    account: {
        name: string;
        email: string;
    };
}>();

const form = useForm({
    name: props.account.name,
    email: props.account.email,
});

const submit = () => {
    form.patch('/profile');
};
</script>

<template>
    <div class="max-w-2xl mx-auto space-y-6">
        <Head title="My account – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">My account</h1>
            <p class="text-sm text-slate-500">
                This is the name and email you use to sign in. It does not change the public website.
                The name, bio, and resume visitors see are edited in Site Profile.
            </p>
        </div>

        <form
            class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="submit"
        >
            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400" for="account-name">Login name</label>
                <input
                    id="account-name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400" for="account-email">Login email</label>
                <input
                    id="account-email"
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>

            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                :disabled="form.processing"
            >
                Save login details
            </button>
        </form>
    </div>
</template>
