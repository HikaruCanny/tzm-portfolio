<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';

defineOptions({ layout: AdminLayout });

interface ActivityEntry {
    id: number;
    action: string;
    subject_type: string;
    subject_id: number | null;
    created_at: string;
    changes?: Record<string, unknown> | null;
    user?: { name: string } | null;
}

const props = defineProps<{
    logs: {
        data: ActivityEntry[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const subjectName = (type: string): string => {
    const name = type.split('\\').pop() ?? 'Item';

    return name.replace(/([a-z])([A-Z])/g, '$1 $2');
};

const dayLabel = (iso: string): string => {
    const date = new Date(iso);
    const start = (value: Date) => new Date(value.getFullYear(), value.getMonth(), value.getDate()).getTime();
    const days = Math.round((start(new Date()) - start(date)) / 86_400_000);

    if (days === 0) {
        return 'Today';
    }

    if (days === 1) {
        return 'Yesterday';
    }

    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
};

const timeLabel = (iso: string): string => {
    const date = new Date(iso);
    const minutes = Math.round((Date.now() - date.getTime()) / 60_000);

    if (minutes < 1) {
        return 'Just now';
    }

    if (minutes < 60) {
        return `${minutes}m ago`;
    }

    const hours = Math.round(minutes / 60);

    if (hours < 24) {
        return `${hours}h ago`;
    }

    return date.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
};

const tone = (action: string): string => {
    if (action === 'created') {
        return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300';
    }

    if (action === 'deleted') {
        return 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300';
    }

    return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300';
};

const groups = computed(() => {
    const buckets = new Map<string, ActivityEntry[]>();

    props.logs.data.forEach((log) => {
        const label = dayLabel(log.created_at);
        const items = buckets.get(label) ?? [];
        items.push(log);
        buckets.set(label, items);
    });

    return [...buckets.entries()].map(([label, items]) => ({ label, items }));
});

const changeKeys = (log: ActivityEntry): string[] => Object.keys(log.changes ?? {});
</script>

<template>
    <div class="mx-auto max-w-3xl space-y-6">
        <Head title="Activity – TZM Admin" />

        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Activity log</h1>
                <p class="text-sm text-slate-500">Who created, edited, or deleted portfolio content.</p>
            </div>
            <p v-if="logs.total" class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                {{ logs.from }}–{{ logs.to }} of {{ logs.total }}
            </p>
        </div>

        <div v-if="logs.data.length" class="space-y-6">
            <section v-for="group in groups" :key="group.label" class="space-y-2">
                <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ group.label }}</h2>
                <ol class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                    <li
                        v-for="log in group.items"
                        :key="log.id"
                        class="flex items-start justify-between gap-4 border-b border-slate-100 px-4 py-3 last:border-b-0 dark:border-slate-800"
                    >
                        <div class="min-w-0">
                            <p class="text-sm text-slate-800 dark:text-slate-100">
                                <span class="font-semibold">{{ log.user?.name || 'System' }}</span>
                                <span :class="tone(log.action)" class="mx-2 inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide">{{ log.action }}</span>
                                <span class="text-slate-500">{{ subjectName(log.subject_type) }}<span v-if="log.subject_id"> #{{ log.subject_id }}</span></span>
                            </p>
                            <p v-if="changeKeys(log).length" class="mt-1 truncate text-xs text-slate-400">
                                {{ changeKeys(log).join(', ') }}
                            </p>
                        </div>
                        <time class="shrink-0 text-xs text-slate-400" :datetime="log.created_at">{{ timeLabel(log.created_at) }}</time>
                    </li>
                </ol>
            </section>
        </div>
        <p v-else class="rounded-2xl border border-slate-200 bg-white px-4 py-8 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900">No activity recorded yet.</p>

        <Pagination :paginator="logs" />
    </div>
</template>
