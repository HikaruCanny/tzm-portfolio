<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    users: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        disabled_at: string | null;
    }>;
}>();

const page = usePage();

const changeRole = (id: number, role: string) => {
    router.patch(`/admin/users/${id}`, { role }, { preserveScroll: true });
};

const disable = (id: number) => {
    if (confirm('Disable this account? They will no longer be able to sign in.')) {
        router.delete(`/admin/users/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-6">
        <Head title="Admins – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Admin accounts</h1>
            <p class="text-sm text-slate-500">Change roles or disable access. The last superadmin stays protected.</p>
        </div>

        <p v-if="page.props.errors?.user" class="text-sm text-red-600">{{ page.props.errors.user }}</p>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl divide-y divide-slate-100 dark:divide-slate-800">
            <div v-for="user in users" :key="user.id" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4">
                <div>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ user.name }}</p>
                    <p class="text-sm text-slate-500">{{ user.email }}</p>
                    <p v-if="user.disabled_at" class="text-xs text-red-600">Disabled</p>
                </div>
                <div class="flex items-center gap-2">
                    <select
                        :value="user.role"
                        class="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"
                        @change="changeRole(user.id, ($event.target as HTMLSelectElement).value)"
                    >
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                    <button
                        type="button"
                        class="px-3 py-2 rounded-lg text-sm text-red-600 border border-red-200 dark:border-red-900"
                        :disabled="!!user.disabled_at"
                        @click="disable(user.id)"
                    >
                        Disable
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
