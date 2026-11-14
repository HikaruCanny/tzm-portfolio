<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    status: number;
}>();

const copy = computed(() => {
    if (props.status === 404) {
        return {
            title: 'Page not found',
            body: 'That page is not part of this portfolio. Head back home or browse the projects.',
        };
    }

    if (props.status === 403) {
        return {
            title: 'Access denied',
            body: 'You do not have permission to open this page.',
        };
    }

    return {
        title: 'Something went wrong',
        body: 'The site hit an unexpected error. Try again in a moment.',
    };
});
</script>

<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center space-y-6">
        <Head :title="copy.title" />

        <p class="text-sm font-semibold tracking-wide text-indigo-600 dark:text-indigo-400">
            {{ status }}
        </p>
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            {{ copy.title }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400">
            {{ copy.body }}
        </p>
        <div class="flex items-center justify-center gap-3">
            <Link
                href="/"
                class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700"
            >
                Back home
            </Link>
            <Link
                href="/projects"
                class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-semibold"
            >
                View projects
            </Link>
        </div>
    </div>
</template>
