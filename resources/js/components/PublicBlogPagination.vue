<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

const props = defineProps<{
    paginator: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();

const pageItems = computed(() => {
    const current = props.paginator.current_page;
    const last = props.paginator.last_page;
    const numbers = new Set<number>([1, last]);

    for (let page = current - 1; page <= current + 1; page++) {
        if (page >= 1 && page <= last) {
            numbers.add(page);
        }
    }

    const sorted = [...numbers].sort((left, right) => left - right);
    const items: Array<{ type: 'page'; page: number } | { type: 'ellipsis'; key: string }> = [];

    sorted.forEach((page, index) => {
        const previous = sorted[index - 1];

        if (previous !== undefined && page - previous > 1) {
            items.push({ type: 'ellipsis', key: `gap-${previous}-${page}` });
        }

        items.push({ type: 'page', page });
    });

    return items;
});

const urlFor = (page: number): string | null => {
    const match = props.paginator.links?.find((link) => link.label.trim() === String(page));

    return match?.url ?? null;
};
</script>

<template>
    <nav
        v-if="paginator.last_page > 1"
        class="flex flex-col items-center gap-5 border-t border-primary-100 pt-10 sm:flex-row sm:justify-between dark:border-white/10"
        aria-label="Blog pagination"
    >
        <Link
            v-if="paginator.prev_page_url"
            :href="paginator.prev_page_url"
            preserve-scroll
            class="inline-flex items-center gap-2 rounded-full border border-cyan-600/30 bg-white px-4 py-2 text-sm font-semibold text-cyan-800 shadow-sm transition hover:border-cyan-600 hover:bg-cyan-600 hover:text-white dark:border-cyan-400/35 dark:bg-white/5 dark:text-cyan-300 dark:hover:border-cyan-400 dark:hover:bg-cyan-500 dark:hover:text-slate-950"
        >
            <ChevronLeft class="h-4 w-4" />
            <span>Previous</span>
        </Link>
        <span
            v-else
            class="inline-flex cursor-not-allowed items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-400 opacity-60 dark:border-white/10 dark:text-slate-500"
            aria-hidden="true"
        >
            <ChevronLeft class="h-4 w-4" />
            <span>Previous</span>
        </span>

        <div class="flex flex-wrap items-center justify-center gap-1.5">
            <template v-for="item in pageItems" :key="item.type === 'page' ? `page-${item.page}` : item.key">
                <span v-if="item.type === 'ellipsis'" class="px-1 text-sm font-semibold tracking-widest text-slate-400">
                    …
                </span>
                <span
                    v-else-if="item.page === paginator.current_page || !urlFor(item.page)"
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-full bg-cyan-600 px-2.5 text-sm font-semibold text-white shadow-sm dark:bg-cyan-500 dark:text-slate-950"
                    aria-current="page"
                >
                    {{ item.page }}
                </span>
                <Link
                    v-else
                    :href="urlFor(item.page)!"
                    preserve-scroll
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-full px-2.5 text-sm font-semibold text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-800 dark:text-slate-300 dark:hover:bg-cyan-950/40 dark:hover:text-cyan-300"
                >
                    {{ item.page }}
                </Link>
            </template>
        </div>

        <Link
            v-if="paginator.next_page_url"
            :href="paginator.next_page_url"
            preserve-scroll
            class="btn-send inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition"
        >
            <span>Next</span>
            <ChevronRight class="h-4 w-4" />
        </Link>
        <span
            v-else
            class="inline-flex cursor-not-allowed items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-400 opacity-60 dark:border-white/10 dark:text-slate-500"
            aria-hidden="true"
        >
            <span>Next</span>
            <ChevronRight class="h-4 w-4" />
        </span>
    </nav>
</template>
