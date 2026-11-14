<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Tags, Trash2 } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    technologies: {
        data: Array<{ id: number; name: string; slug: string; projects_count: number }>;
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
    form.post('/admin/technologies', {
        onSuccess: () => form.reset(),
    });
};

const remove = (id: number) => {
    if (confirm('Remove this technology from the filter list?')) {
        router.delete(`/admin/technologies/${id}`);
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-8">
        <Head title="Technologies – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Technologies</h1>
            <p class="text-sm text-slate-500">Tags used to filter projects on the public site.</p>
        </div>

        <form @submit.prevent="submit" class="flex gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5">
            <input
                v-model="form.name"
                required
                type="text"
                placeholder="E.g. Laravel"
                class="flex-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"
            />
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold">
                Add
            </button>
        </form>
        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl divide-y divide-slate-100 dark:divide-slate-800">
            <div v-for="tech in technologies.data" :key="tech.id" class="flex items-center justify-between px-5 py-4">
                <div class="flex items-center gap-3">
                    <Tags class="w-4 h-4 text-indigo-600" />
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ tech.name }}</p>
                        <p class="text-xs text-slate-500">{{ tech.projects_count }} linked projects</p>
                    </div>
                </div>
                <button type="button" class="text-red-600" @click="remove(tech.id)">
                    <Trash2 class="w-4 h-4" />
                </button>
            </div>
            <p v-if="technologies.data.length === 0" class="px-5 py-8 text-sm text-slate-500">No technologies yet.</p>
        </div>
        <Pagination :paginator="technologies" />
    </div>
</template>
