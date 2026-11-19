<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicBlogPagination from '@/components/PublicBlogPagination.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowRight, Calendar, Clock, Eye, FileText, Search, Sparkles } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Tag {
    id: number;
    name: string;
    slug: string;
}

interface Author {
    name: string;
    title?: string | null;
    photo_path?: string | null;
}

interface PostCard {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image?: string | null;
    cover_url: string;
    published_at?: string | null;
    reading_time: number;
    views_count: number;
    is_featured?: boolean;
    tags: Tag[];
    author: Author;
}

const props = defineProps<{
    posts: {
        data: PostCard[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
    featuredPost: PostCard | null;
    tags: Tag[];
    filters: {
        search: string;
        sort: 'newest' | 'popular';
        tag: { name: string; slug: string } | null;
    };
}>();

const search = ref(props.filters.search);
const sort = ref(props.filters.sort || 'newest');

const listUrl = (): string => (props.filters.tag ? `/blog/tag/${props.filters.tag.slug}` : '/blog');

const applyFilters = () => {
    router.get(listUrl(), { search: search.value, sort: sort.value }, { preserveState: true, replace: true });
};

const tagChip = 'chip-light border border-transparent hover:border-primary-300 dark:hover:border-white/20';
const tagChipActive = 'bg-primary-600 text-white shadow-sm shadow-primary-600/25';

const formatDate = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

const purpleIconTone = (index: number): string => `skill-tone-${index % 3}`;
const titleCyan = 'text-cyan-700 transition-colors hover:text-cyan-800 dark:text-cyan-300 dark:hover:text-cyan-200';
const actionLightPurple = 'skill-tone-0';

const formatViews = (count: number): string => {
    if (count >= 1000) {
        return `${(count / 1000).toFixed(count >= 10000 ? 0 : 1).replace(/\.0$/, '')}k`;
    }

    return String(count);
};
</script>

<template>
    <div class="section-bg-a">
    <div class="mx-auto max-w-7xl space-y-14 px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
            <Head title="Technical Blog – TZM" />

            <header class="max-w-3xl space-y-4">
                <p class="skill-tone-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-widest">
                    <Sparkles class="h-3.5 w-3.5" />
                    Journal
                </p>
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl dark:text-white">
                    Technical Writings
                </h1>
                <p class="text-lg leading-relaxed text-slate-600 dark:text-slate-400">
                    Deep dives on Laravel, Vue, and shipping dependable software in production.
                </p>
            </header>

            <div
                class="sticky top-16 z-10 rounded-2xl border border-primary-100 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-[color:var(--section-a)]"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap gap-2">
                        <Link
                            href="/blog"
                            class="rounded-full px-3.5 py-1.5 text-sm font-medium transition-colors"
                            :class="filters.tag ? tagChip : tagChipActive"
                        >
                            All
                        </Link>
                        <Link
                            v-for="tag in tags"
                            :key="tag.id"
                            :href="`/blog/tag/${tag.slug}`"
                            class="rounded-full px-3.5 py-1.5 text-sm font-medium transition-colors"
                            :class="filters.tag?.slug === tag.slug ? tagChipActive : tagChip"
                        >
                            {{ tag.name }}
                        </Link>
                    </div>
                    <div class="flex w-full flex-col gap-2 sm:flex-row sm:items-center lg:w-auto">
                        <label class="sr-only" for="blog-sort">Sort articles</label>
                        <select
                            id="blog-sort"
                            v-model="sort"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:w-auto dark:border-white/15 dark:bg-white/10 dark:text-slate-200"
                            @change="applyFilters"
                        >
                            <option value="newest">Newest</option>
                            <option value="popular">Most viewed</option>
                        </select>
                        <form class="relative w-full sm:w-72" @submit.prevent="applyFilters">
                            <Search class="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Search articles..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-white/15 dark:bg-white/10 dark:text-slate-200"
                            />
                        </form>
                    </div>
                </div>
            </div>

            <article
                v-if="featuredPost"
                class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-xl dark:border-slate-800"
            >
                <Link :href="`/blog/${featuredPost.slug}`" class="block">
                    <div class="relative min-h-[20rem] sm:min-h-[24rem]">
                        <img
                            :src="featuredPost.cover_url"
                            :alt="featuredPost.title"
                            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-slate-950/20"
                        />
                        <div class="relative flex min-h-[20rem] flex-col justify-end p-6 sm:min-h-[24rem] sm:p-10">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.25em] text-primary-300">
                                Featured story
                            </p>
                            <h2 class="max-w-3xl text-3xl font-extrabold tracking-tight text-cyan-300 sm:text-4xl">
                                {{ featuredPost.title }}
                            </h2>
                            <p class="mt-3 max-w-2xl line-clamp-2 text-sm leading-relaxed text-slate-200 sm:text-base">
                                {{ featuredPost.excerpt }}
                            </p>
                            <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                                <span class="text-slate-300">
                                    Author :
                                    <span class="font-medium text-white">{{ featuredPost.author.name }}</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <Clock class="h-3.5 w-3.5" />
                                    {{ featuredPost.reading_time }} min
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <Eye class="h-3.5 w-3.5" />
                                    {{ formatViews(featuredPost.views_count) }}
                                </span>
                                <span
                                    :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold', actionLightPurple]"
                                >
                                    Read article
                                    <ArrowRight class="h-4 w-4 transition group-hover:translate-x-0.5" />
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>
            </article>

            <div v-if="posts.data.length" class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="(post, index) in posts.data"
                    :key="post.id"
                    class="group flex flex-col overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300/80 hover:shadow-lg dark:border-white/10 dark:bg-white/5"
                >
                    <Link
                        :href="`/blog/${post.slug}`"
                        class="relative block aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800"
                    >
                        <img
                            :src="post.cover_url"
                            :alt="post.title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                    </Link>

                    <div class="flex flex-1 flex-col gap-3 p-5 sm:p-6">
                        <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(index)]">
                            <FileText class="h-5 w-5" />
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            <Link
                                v-for="tag in post.tags.slice(0, 2)"
                                :key="tag.id"
                                :href="`/blog/tag/${tag.slug}`"
                                class="chip-light rounded-full px-2.5 py-0.5 text-xs font-semibold"
                            >
                                {{ tag.name }}
                            </Link>
                            <span v-if="post.published_at" class="flex items-center gap-1">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ formatDate(post.published_at) }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold leading-snug">
                            <Link
                                :href="`/blog/${post.slug}`"
                                :class="titleCyan"
                            >
                                {{ post.title }}
                            </Link>
                        </h2>

                        <p class="line-clamp-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                            {{ post.excerpt }}
                        </p>

                        <Link
                            :href="`/blog/${post.slug}`"
                            :class="['inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-1 text-sm font-semibold', actionLightPurple]"
                        >
                            <span>Read</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>

                        <div
                            class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs text-slate-500 dark:border-slate-800"
                        >
                            <span class="text-slate-500">
                                Author :
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ post.author.name }}</span>
                            </span>
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <Clock class="h-3.5 w-3.5" />
                                    {{ post.reading_time }}m
                                </span>
                                <span class="flex items-center gap-1">
                                    <Eye class="h-3.5 w-3.5" />
                                    {{ formatViews(post.views_count) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <p v-else-if="!featuredPost" class="rounded-2xl border border-dashed border-slate-300 px-6 py-16 text-center text-slate-500 dark:border-slate-700">
                No articles yet.
            </p>

            <PublicBlogPagination :paginator="posts" />
    </div>
    </div>
</template>