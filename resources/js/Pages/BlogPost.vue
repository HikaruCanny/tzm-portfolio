<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowLeft, Calendar, Clock, Share2 } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

defineProps<{
    slug: string;
}>();
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8">
        <Head title="Article – TZM" />

        <div>
            <Link
                href="/blog"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Back to all posts</span>
            </Link>
        </div>

        <header class="space-y-4">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Technical Case Study & Article
            </h1>
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1">
                    <Calendar class="w-3.5 h-3.5" />
                    Published Nov 2026
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                    <Clock class="w-3.5 h-3.5" />
                    5 min read
                </span>
            </div>
        </header>

        <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed space-y-6 pt-6 border-t border-slate-200 dark:border-slate-800">
            <p>
                [PLACEHOLDER: Article content will be managed dynamically via Markdown in Phase 4 of the portfolio build].
            </p>
            <p>
                This space will display formatted markdown tutorials, architectural diagrams, syntax-highlighted code blocks, and lessons learned across production projects.
            </p>
        </div>
    </div>
</template>
