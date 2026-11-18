<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';
import { ref, watch } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Save } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

interface TagOption {
    id: number;
    name: string;
}

interface PostRecord {
    id: number;
    title: string;
    excerpt: string;
    body_markdown: string;
    status: string;
    cover_image?: string | null;
    tags?: TagOption[];
}

const props = defineProps<{
    post: PostRecord | null;
    tags: TagOption[];
    previewHtml: string;
}>();

const isEditing = !!props.post;
const preview = ref(props.previewHtml || '');
const previewing = ref(false);

const form = useForm({
    title: props.post?.title || '',
    excerpt: props.post?.excerpt || '',
    body: props.post?.body_markdown || '',
    status: props.post?.status || 'draft',
    tag_ids: props.post?.tags?.map((tag) => tag.id) || ([] as number[]),
    cover_image: null as File | null,
    remove_cover: false,
});

const refreshPreview = useDebounceFn(async () => {
    previewing.value = true;

    try {
        const { data } = await axios.post('/admin/posts/preview', {
            body: form.body,
        });
        preview.value = data.html || '';
    } catch {
        preview.value = '<p class="text-red-600">Preview failed.</p>';
    } finally {
        previewing.value = false;
    }
}, 400);

watch(() => form.body, () => {
    refreshPreview();
});

const submit = () => {
    if (isEditing) {
        form.put(`/admin/posts/${props.post!.id}`, { forceFormData: true });
    } else {
        form.post('/admin/posts', { forceFormData: true });
    }
};

const fileName = (path: string): string => path.split('/').pop() ?? path;
</script>

<template>
    <div class="mx-auto max-w-5xl space-y-6">
        <Head :title="isEditing ? 'Edit Post – TZM Admin' : 'New Post – TZM Admin'" />

        <div class="flex items-center gap-3">
            <Link href="/admin/posts" class="rounded-lg p-2 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-800">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                {{ isEditing ? 'Edit Post' : 'Create Post' }}
            </h1>
        </div>

        <form
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
            @submit.prevent="submit"
        >
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Title</label>
                <input
                    v-model="form.title"
                    required
                    type="text"
                    maxlength="180"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
                <p v-if="form.errors.title" class="text-xs text-red-600">{{ form.errors.title }}</p>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Excerpt</label>
                <textarea
                    v-model="form.excerpt"
                    required
                    rows="2"
                    maxlength="500"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
                <p v-if="form.errors.excerpt" class="text-xs text-red-600">{{ form.errors.excerpt }}</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Body (Markdown)</label>
                    <textarea
                        v-model="form.body"
                        required
                        rows="18"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 font-mono text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    />
                    <p v-if="form.errors.body" class="text-xs text-red-600">{{ form.errors.body }}</p>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Preview</label>
                        <span v-if="previewing" class="text-xs text-slate-400">Updating…</span>
                    </div>
                    <div
                        class="blog-content min-h-[28rem] overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-800 dark:bg-slate-950"
                        v-html="preview || '<p class=\'text-slate-400\'>Start writing to preview.</p>'"
                    />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select
                        v-model="form.status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Tags</label>
                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="tag in tags"
                            :key="tag.id"
                            class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs transition-colors"
                            :class="form.tag_ids.includes(tag.id)
                                ? 'border-indigo-600 bg-indigo-600 text-white'
                                : 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300'"
                        >
                            <input v-model="form.tag_ids" type="checkbox" :value="tag.id" class="hidden" />
                            <span>{{ tag.name }}</span>
                        </label>
                        <p v-if="tags.length === 0" class="text-xs text-slate-500">
                            Add tags under Admin → Tags first.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Cover image</label>
                <div v-if="props.post?.cover_image && !form.remove_cover" class="max-w-sm space-y-2">
                    <img
                        :src="`/storage/${props.post.cover_image}`"
                        alt="Current cover"
                        class="h-40 w-full rounded-xl border border-slate-200 object-cover dark:border-slate-800"
                    />
                    <p class="text-xs text-slate-500">{{ fileName(props.post.cover_image) }}</p>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input v-model="form.remove_cover" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                        Remove cover
                    </label>
                </div>
                <input
                    type="file"
                    accept="image/*"
                    class="block text-sm"
                    @change="form.cover_image = ($event.target as HTMLInputElement).files?.[0] ?? null"
                />
                <p v-if="form.errors.cover_image" class="text-xs text-red-600">{{ form.errors.cover_image }}</p>
            </div>

            <div class="flex justify-end border-t border-slate-200 pt-4 dark:border-slate-800">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 font-bold text-white shadow hover:bg-indigo-700 disabled:opacity-60"
                >
                    <Save class="h-4 w-4" />
                    <span>{{ isEditing ? 'Update Post' : 'Save Post' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
