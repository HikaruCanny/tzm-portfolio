<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Hash, Trash2 } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    tags: {
        data: Array<{ id: number; name: string; slug: string; posts_count: number }>;
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const form = useForm({
    name: '',
});

const submit = () => {
    form.post('/admin/tags', {
        onSuccess: () => form.reset(),
    });
};

const remove = (id: number) => {
    if (confirm('Remove this tag?')) {
        router.delete(`/admin/tags/${id}`);
    }
};
</script>

<template>
    <div class="mx-auto max-w-4xl space-y-8">
        <Head title="Tags – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Blog Tags</h1>
            <p class="text-sm text-slate-500">Labels used to filter posts on the public blog.</p>
        </div>

        <form
            class="flex gap-2 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="submit"
        >
            <input
                v-model="form.name"
                required
                type="text"
                placeholder="E.g. Laravel"
                class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            />
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                Add
            </button>
        </form>
        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>

        <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900">
            <div
                v-for="tag in tags.data"
                :key="tag.id"
                class="flex items-center justify-between px-5 py-4"
            >
                <div class="flex items-center gap-3">
                    <Hash class="h-4 w-4 text-indigo-600" />
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ tag.name }}</p>
                        <p class="text-xs text-slate-500">{{ tag.posts_count }} linked posts</p>
                    </div>
                </div>
                <button type="button" class="text-red-600" @click="remove(tag.id)">
                    <Trash2 class="h-4 w-4" />
                </button>
            </div>
            <p v-if="tags.data.length === 0" class="px-5 py-8 text-sm text-slate-500">No tags yet.</p>
        </div>
        <Pagination :paginator="tags" />
    </div>
</template>
