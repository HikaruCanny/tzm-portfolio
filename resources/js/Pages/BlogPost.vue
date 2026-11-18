<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import javascript from 'highlight.js/lib/languages/javascript';
import php from 'highlight.js/lib/languages/php';
import typescript from 'highlight.js/lib/languages/typescript';
import xml from 'highlight.js/lib/languages/xml';
import { nextTick, onMounted, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowLeft, Calendar, Clock, Check, Eye, Share2 } from '@lucide/vue';
import 'highlight.js/styles/github-dark.min.css';

hljs.registerLanguage('php', php);
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('js', javascript);
hljs.registerLanguage('typescript', typescript);
hljs.registerLanguage('ts', typescript);
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('shell', bash);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('html', xml);
hljs.registerLanguage('vue', xml);

defineOptions({ layout: PublicLayout });

interface Tag {
    id: number;
    name: string;
    slug: string;
}

interface TocItem {
    id: string;
    text: string;
    level: number;
}

interface RelatedPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
}

const props = defineProps<{
    post: {
        title: string;
        slug: string;
        excerpt: string;
        cover_image?: string | null;
        published_at?: string | null;
        reading_time: number;
        views_count: number;
        html: string;
        toc: TocItem[];
        tags: Tag[];
    };
    relatedPosts: RelatedPost[];
}>();

const articleEl = ref<HTMLElement | null>(null);
const copied = ref(false);

const formatDate = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatViews = (count: number): string => {
    if (count >= 1000) {
        return `${(count / 1000).toFixed(count >= 10000 ? 0 : 1).replace(/\.0$/, '')}k`;
    }

    return String(count);
};

const shareUrl = (): string => window.location.href;

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl());
        copied.value = true;
        window.setTimeout(() => {
            copied.value = false;
        }, 1600);
    } catch {
        // Ignore clipboard failures.
    }
};

onMounted(async () => {
    await nextTick();

    if (!articleEl.value) {
        return;
    }

    articleEl.value.querySelectorAll('pre code').forEach((block) => {
        hljs.highlightElement(block as HTMLElement);
    });

    articleEl.value.querySelectorAll('pre').forEach((pre) => {
        if (pre.querySelector('.copy-code')) {
            return;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'copy-code';
        button.textContent = 'Copy';
        button.addEventListener('click', async () => {
            const code = pre.querySelector('code')?.textContent ?? '';
            try {
                await navigator.clipboard.writeText(code);
                button.textContent = 'Copied';
                window.setTimeout(() => {
                    button.textContent = 'Copy';
                }, 1400);
            } catch {
                button.textContent = 'Failed';
            }
        });
        pre.appendChild(button);
    });
});
</script>

<template>
    <div class="mx-auto max-w-4xl space-y-8 px-4 py-16 sm:px-6 lg:px-8">
        <Head :title="`${post.title} – TZM`" />

        <div>
            <Link
                href="/blog"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400"
            >
                <ArrowLeft class="h-4 w-4" />
                <span>Back to all posts</span>
            </Link>
        </div>

        <header class="space-y-4">
            <div class="flex flex-wrap gap-2">
                <Link
                    v-for="tag in post.tags"
                    :key="tag.id"
                    :href="`/blog/tag/${tag.slug}`"
                    class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                >
                    {{ tag.name }}
                </Link>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
                {{ post.title }}
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-400">{{ post.excerpt }}</p>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                <span v-if="post.published_at" class="flex items-center gap-1">
                    <Calendar class="h-3.5 w-3.5" />
                    {{ formatDate(post.published_at) }}
                </span>
                <span class="flex items-center gap-1">
                    <Clock class="h-3.5 w-3.5" />
                    {{ post.reading_time }} min read
                </span>
                <span class="flex items-center gap-1">
                    <Eye class="h-3.5 w-3.5" />
                    {{ formatViews(post.views_count) }} views
                </span>
                <button
                    type="button"
                    class="inline-flex items-center gap-1 text-indigo-600 hover:underline dark:text-indigo-400"
                    @click="copyLink"
                >
                    <component :is="copied ? Check : Share2" class="h-3.5 w-3.5" />
                    {{ copied ? 'Link copied' : 'Share' }}
                </button>
            </div>
        </header>

        <img
            v-if="post.cover_image"
            :src="`/storage/${post.cover_image}`"
            :alt="post.title"
            class="aspect-[16/9] w-full rounded-2xl border border-slate-200 object-cover dark:border-slate-800"
        />

        <div v-if="post.toc.length" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">On this page</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li v-for="item in post.toc" :key="item.id" :class="item.level === 3 ? 'pl-4' : ''">
                    <a :href="`#${item.id}`" class="text-indigo-700 hover:underline dark:text-indigo-300">
                        {{ item.text }}
                    </a>
                </li>
            </ul>
        </div>

        <article
            ref="articleEl"
            class="blog-content border-t border-slate-200 pt-6 dark:border-slate-800"
            v-html="post.html"
        />

        <section v-if="relatedPosts.length" class="space-y-4 border-t border-slate-200 pt-8 dark:border-slate-800">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Related posts</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <Link
                    v-for="related in relatedPosts"
                    :key="related.id"
                    :href="`/blog/${related.slug}`"
                    class="rounded-xl border border-slate-200 bg-white p-4 hover:border-indigo-400 dark:border-slate-800 dark:bg-slate-900"
                >
                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ related.title }}</h3>
                    <p class="mt-2 line-clamp-3 text-sm text-slate-600 dark:text-slate-400">{{ related.excerpt }}</p>
                </Link>
            </div>
        </section>
    </div>
</template>
