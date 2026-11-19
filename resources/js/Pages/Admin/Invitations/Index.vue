<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Ticket, Copy, Check, Trash2, Shield, Clock } from '@lucide/vue';
import { computed, ref, watch } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
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
    role: 'admin' as 'admin' | 'superadmin',
    expires_in_days: 7,
});

const dayPresets = [1, 7, 14, 30] as const;

const copied = ref(false);

const flashCode = computed(() => {
    const flash = usePage().props.flash as { invite_code?: string } | undefined;

    return props.inviteCode ?? flash?.invite_code ?? null;
});

watch(flashCode, () => {
    copied.value = false;
});

const submit = () => {
    form.post('/admin/invitations', { preserveScroll: true });
};

const remove = (id: number) => {
    if (confirm('Remove this invitation? Unused codes will stop working immediately.')) {
        router.delete(`/admin/invitations/${id}`);
    }
};

const setDays = (days: number) => {
    form.expires_in_days = days;
};

const copyCode = async () => {
    if (!flashCode.value) {
        return;
    }

    await navigator.clipboard.writeText(flashCode.value);
    copied.value = true;
};

const formatExpires = (iso: string) =>
    new Date(iso).toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const inviteStatus = (invite: (typeof props.invitations.data)[0]) => {
    if (invite.used_at) {
        return { label: 'Used', class: 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' };
    }
    if (new Date(invite.expires_at) < new Date()) {
        return { label: 'Expired', class: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200' };
    }

    return { label: 'Active', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' };
};

const roleLabel = (role: string) => (role === 'superadmin' ? 'Superadmin' : 'Admin');
</script>

<template>
    <div class="mx-auto max-w-4xl space-y-6">
        <Head title="Invitations – TZM Admin" />

        <header class="space-y-2">
            <div
                class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300"
            >
                <Ticket class="h-3.5 w-3.5" />
                Team access
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Admin invitations</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Public registration is closed. Issue a single-use code; it expires after the number of
                <strong class="font-semibold text-slate-700 dark:text-slate-300">days</strong> you choose (not months).
            </p>
        </header>

        <section
            v-if="flashCode"
            class="space-y-3 rounded-2xl border border-indigo-300 bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm dark:border-indigo-800 dark:from-indigo-950/60 dark:to-slate-900"
        >
            <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                New code — copy now. It is only shown once.
            </p>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <code class="flex-1 break-all rounded-xl bg-white px-4 py-3 font-mono text-sm text-slate-900 shadow-inner dark:bg-slate-950 dark:text-white">
                    {{ flashCode }}
                </code>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500"
                    @click="copyCode"
                >
                    <component :is="copied ? Check : Copy" class="h-4 w-4" />
                    {{ copied ? 'Copied' : 'Copy code' }}
                </button>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Create invitation</h2>
            <form class="mt-4 space-y-4" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block space-y-1.5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</span>
                        <div class="relative">
                            <Shield class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <select
                                v-model="form.role"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm dark:border-slate-700 dark:bg-slate-950"
                            >
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </label>

                    <label class="block space-y-1.5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Valid for (days)</span>
                        <div class="relative">
                            <Clock class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input
                                v-model="form.expires_in_days"
                                type="number"
                                min="1"
                                max="30"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm dark:border-slate-700 dark:bg-slate-950"
                            />
                        </div>
                        <span class="text-[11px] text-slate-400">Allowed: 1–30 days (default 7).</span>
                    </label>
                </div>

                <p class="text-xs font-semibold text-slate-500">Quick presets</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="days in dayPresets"
                        :key="days"
                        type="button"
                        class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition"
                        :class="
                            form.expires_in_days === days
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-slate-200 text-slate-600 hover:border-indigo-300 dark:border-slate-700 dark:text-slate-300'
                        "
                        @click="setDays(days)"
                    >
                        {{ days }} {{ days === 1 ? 'day' : 'days' }}
                    </button>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 disabled:opacity-50 sm:w-auto"
                    :disabled="form.processing"
                >
                    Generate invite code
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-5 py-3 dark:border-slate-800">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Issued codes</h2>
                <p class="text-xs text-slate-500">{{ invitations.total }} total</p>
            </div>

            <ul v-if="invitations.data.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800">
                <li v-for="invite in invitations.data" :key="invite.id" class="flex items-center gap-3 px-5 py-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold uppercase"
                        :class="
                            invite.role === 'superadmin'
                                ? 'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-300'
                                : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                        "
                    >
                        {{ invite.role === 'superadmin' ? 'SA' : 'AD' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-900 dark:text-white">{{ roleLabel(invite.role) }}</p>
                        <p class="text-xs text-slate-500">
                            Expires {{ formatExpires(invite.expires_at) }}
                            <span v-if="invite.creator?.name"> · by {{ invite.creator.name }}</span>
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase" :class="inviteStatus(invite).class">
                        {{ inviteStatus(invite).label }}
                    </span>
                    <button
                        type="button"
                        class="shrink-0 rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40"
                        aria-label="Remove invitation"
                        @click="remove(invite.id)"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </li>
            </ul>
            <p v-else class="px-5 py-10 text-center text-sm text-slate-500">No invitations yet.</p>
        </section>

        <Pagination :paginator="invitations" />
    </div>
</template>