<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    invitations: {
        data: Array<{
            id: number;
            role: string;
            expires_at: string;
            used_at: string | null;
            creator?: { name: string } | null;
        }>;
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    inviteCode?: string | null;
}>();

const form = useForm({
    role: 'admin',
    expires_in_days: 7,
});

const submit = () => {
    form.post('/admin/invitations');
};

const remove = (id: number) => {
    router.delete(`/admin/invitations/${id}`);
};
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <Head title="Invitations – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Invite an admin</h1>
            <p class="text-sm text-slate-500">Registration stays closed unless someone has a single-use code.</p>
        </div>

        <div v-if="inviteCode" class="rounded-2xl border border-indigo-200 bg-indigo-50 dark:bg-indigo-950/40 dark:border-indigo-800 p-5 space-y-2">
            <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">Copy this code now. It will not be shown again.</p>
            <p class="font-mono text-sm break-all text-slate-900 dark:text-white">{{ inviteCode }}</p>
        </div>

        <form @submit.prevent="submit" class="grid gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:grid-cols-3">
            <label class="block space-y-1">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Role</span>
                <select v-model="form.role" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm">
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </label>
            <label class="block space-y-1">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Valid for (days)</span>
                <input
                    v-model="form.expires_in_days"
                    type="number"
                    min="1"
                    max="30"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"
                    title="Number of days until this invite code stops working (1–30, not months)"
                />
                <span class="text-[11px] text-slate-400">1–30 days until expiry — not months. Default 7.</span>
            </label>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold">Create invite</button>
            </div>
        </form>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl divide-y divide-slate-100 dark:divide-slate-800">
            <div v-for="invite in invitations.data" :key="invite.id" class="flex items-center justify-between px-5 py-4 text-sm">
                <div>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ invite.role }}</p>
                    <p class="text-slate-500">Expires {{ new Date(invite.expires_at).toLocaleDateString() }} · {{ invite.used_at ? 'Used' : 'Unused' }}</p>
                </div>
                <button type="button" class="text-red-600" @click="remove(invite.id)">Remove</button>
            </div>
            <p v-if="invitations.data.length === 0" class="px-5 py-8 text-slate-500">No invitations yet.</p>
        </div>
        <Pagination :paginator="invitations" />
    </div>
</template>
