<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    paginator: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();
</script>

<template>
    <div v-if="paginator.last_page > 1" class="flex items-center justify-between gap-3">
        <Link
            v-if="paginator.prev_page_url"
            :href="paginator.prev_page_url"
            preserve-scroll
            class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-white dark:border-slate-700 dark:text-slate-200"
        >
            Previous
        </Link>
        <span v-else class="rounded-full px-4 py-2 text-sm text-slate-300">Previous</span>
        <span class="text-center text-xs font-semibold text-slate-400">
            {{ paginator.from }}–{{ paginator.to }} of {{ paginator.total }}
        </span>
        <Link
            v-if="paginator.next_page_url"
            :href="paginator.next_page_url"
            preserve-scroll
            class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-slate-950"
        >
            Next
        </Link>
        <span v-else class="rounded-full px-4 py-2 text-sm text-slate-300">Next</span>
    </div>
</template>