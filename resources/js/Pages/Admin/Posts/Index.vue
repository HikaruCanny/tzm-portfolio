<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Edit, Plus, Trash2 } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    posts: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            status: string;
            published_at?: string | null;
            views_count: number;
            tags: Array<{ id: number; name: string; slug: string }>;
        }>;
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const deletePost = (id: number) => {
    if (confirm('Delete this post?')) {
        router.delete(`/admin/posts/${id}`);
    }
};
</script>

<template>
    <div class="mx-auto max-w-7xl space-y-6">
        <Head title="Posts – TZM Admin" />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Blog Posts
                </h1>
                <p class="text-sm text-slate-500">
                    Draft and publish Markdown articles for the public blog.
                </p>
            </div>

            <Link
                href="/admin/posts/create"
                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700"
            >
                <Plus class="h-4 w-4" />
                <span>New Post</span>
            </Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Tags</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="post in posts.data"
                        :key="post.id"
                        class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30"
                    >
                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                            {{ post.title }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="tag in post.tags"
                                    :key="tag.id"
                                    class="rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="post.status === 'published'
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400'
                                    : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400'"
                            >
                                {{ post.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 tabular-nums text-slate-600 dark:text-slate-300">
                            {{ post.views_count }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <Link
                                    :href="`/admin/posts/${post.id}/edit`"
                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-indigo-600 dark:hover:bg-slate-800"
                                    title="Edit"
                                >
                                    <Edit class="h-4 w-4" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                    title="Delete"
                                    @click="deletePost(post.id)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="posts.data.length === 0">
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                            No posts yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :paginator="posts" />
    </div>
</template>
