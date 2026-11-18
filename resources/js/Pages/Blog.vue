<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowRight, Calendar, Clock, Eye, Search } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Tag {
    id: number;
    name: string;
    slug: string;
}

interface PostCard {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_image?: string | null;
    published_at?: string | null;
    reading_time: number;
    views_count: number;
    tags: Tag[];
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
    };
    tags: Tag[];
    filters: {
        search: string;
        tag: { name: string; slug: string } | null;
    };
}>();

const search = ref(props.filters.search);

const applySearch = () => {
    const url = props.filters.tag ? `/blog/tag/${props.filters.tag.slug}` : '/blog';
    router.get(url, { search: search.value }, { preserveState: true, replace: true });
};

const formatDate = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatViews = (count: number): string => {
    if (count >= 1000) {
        return `${(count / 1000).toFixed(count >= 10000 ? 0 : 1).replace(/\.0$/, '')}k`;
    }

    return String(count);
};
</script>

<template>
    <div class="mx-auto max-w-5xl space-y-12 px-4 py-16 sm:px-6 lg:px-8">
        <Head title="Technical Blog – TZM" />

        <div class="max-w-2xl space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Technical Writings
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Articles, architecture insights, and learnings from real production code.
            </p>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                <Link
                    href="/blog"
                    class="rounded-lg px-3.5 py-1.5 text-sm font-medium"
                    :class="filters.tag ? 'border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400' : 'bg-indigo-600 text-white'"
                >
                    All
                </Link>
                <Link
                    v-for="tag in tags"
                    :key="tag.id"
                    :href="`/blog/tag/${tag.slug}`"
                    class="rounded-lg px-3.5 py-1.5 text-sm font-medium"
                    :class="filters.tag?.slug === tag.slug ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400'"
                >
                    {{ tag.name }}
                </Link>
            </div>
            <form class="relative w-full sm:w-72" @submit.prevent="applySearch">
                <Search class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search articles..."
                    class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-4 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                />
            </form>
        </div>

        <div v-if="posts.data.length" class="space-y-6">
            <article
                v-for="post in posts.data"
                :key="post.id"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:border-indigo-500/50 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex flex-col sm:flex-row">
                    <Link
                        :href="`/blog/${post.slug}`"
                        class="relative block shrink-0 overflow-hidden bg-slate-100 sm:w-48 md:w-56 dark:bg-slate-800"
                    >
                        <img
                            v-if="post.cover_image"
                            :src="`/storage/${post.cover_image}`"
                            :alt="post.title"
                            class="h-44 w-full object-cover transition-transform duration-300 hover:scale-[1.03] sm:h-full sm:min-h-[10.5rem]"
                        />
                        <div
                            v-else
                            class="flex h-44 w-full items-center justify-center bg-gradient-to-br from-indigo-100 via-slate-100 to-cyan-100 text-indigo-500 sm:h-full sm:min-h-[10.5rem] dark:from-indigo-950 dark:via-slate-900 dark:to-cyan-950 dark:text-indigo-300"
                        >
                            <span class="text-3xl font-extrabold opacity-60">{{ post.title.slice(0, 1) }}</span>
                        </div>
                    </Link>

                    <div class="flex min-w-0 flex-1 flex-col justify-center space-y-3 p-5 sm:p-6">
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <Link
                                v-for="tag in post.tags"
                                :key="tag.id"
                                :href="`/blog/tag/${tag.slug}`"
                                class="rounded-full bg-indigo-50 px-2.5 py-0.5 font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                            >
                                {{ tag.name }}
                            </Link>
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
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 sm:text-2xl dark:text-white">
                            <Link :href="`/blog/${post.slug}`" class="hover:text-indigo-600">{{ post.title }}</Link>
                        </h2>
                        <p class="line-clamp-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ post.excerpt }}</p>
                        <Link
                            :href="`/blog/${post.slug}`"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400"
                        >
                            <span>Read article</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </article>
        </div>
        <p v-else class="text-slate-500">No articles yet.</p>

        <div v-if="posts.last_page > 1" class="flex items-center justify-between gap-3">
            <Link
                v-if="posts.prev_page_url"
                :href="posts.prev_page_url"
                preserve-scroll
                class="rounded-full border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                Previous
            </Link>
            <span v-else class="px-4 py-2 text-sm text-slate-300">Previous</span>
            <span class="text-center text-xs font-semibold text-slate-400">{{ posts.from }}–{{ posts.to }} of {{ posts.total }}</span>
            <Link
                v-if="posts.next_page_url"
                :href="posts.next_page_url"
                preserve-scroll
                class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                Next
            </Link>
            <span v-else class="px-4 py-2 text-sm text-slate-300">Next</span>
        </div>
    </div>
</template>
