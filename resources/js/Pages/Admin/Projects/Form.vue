<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ArrowLeft, Save } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    project: any | null;
    technologies: any[];
}>();

const isEditing = !!props.project;

const form = useForm({
    title: props.project?.title || '',
    summary: props.project?.summary || '',
    problem: props.project?.problem || '',
    solution: props.project?.solution || '',
    role: props.project?.role || '',
    status: props.project?.status || 'draft',
    demo_url: props.project?.demo_url || '',
    repo_url: props.project?.repo_url || '',
    is_featured: props.project?.is_featured ? true : false,
    technology_ids: props.project?.technologies?.map((t: any) => t.id) || [],
    cover_image: null as File | null,
    images: [] as File[],
    remove_image_ids: [] as number[],
});

const submit = () => {
    if (isEditing) {
        form.put(`/admin/projects/${props.project.id}`, { forceFormData: true });
    } else {
        form.post('/admin/projects', { forceFormData: true });
    }
};

const fileName = (path: string): string => path.split('/').pop() ?? path;
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-6">
        <Head :title="isEditing ? 'Edit Project – TZM Admin' : 'New Project – TZM Admin'" />

        <div class="flex items-center gap-3">
            <Link href="/admin/projects" class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500">
                <ArrowLeft class="w-4 h-4" />
            </Link>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                {{ isEditing ? 'Edit Project' : 'Create New Project' }}
            </h1>
        </div>

        <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Project Title</label>
                <input
                    v-model="form.title"
                    required
                    type="text"
                    placeholder="E.g. Real-Time Order Management Hub"
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Role / Contribution</label>
                    <input
                        v-model="form.role"
                        type="text"
                        placeholder="E.g. Lead Full-Stack Architect"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                    <select
                        v-model="form.status"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Summary Pitch</label>
                <textarea
                    v-model="form.summary"
                    rows="2"
                    placeholder="Short 2-line summary for project cards..."
                    class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                ></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Problem Addressed</label>
                    <textarea
                        v-model="form.problem"
                        rows="3"
                        placeholder="What architectural or business challenge did this solve?"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Solution Delivered</label>
                    <textarea
                        v-model="form.solution"
                        rows="3"
                        placeholder="How was the application designed, tested, and implemented?"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    ></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Demo URL</label>
                    <input
                        v-model="form.demo_url"
                        type="url"
                        placeholder="https://..."
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">GitHub Repo URL</label>
                    <input
                        v-model="form.repo_url"
                        type="url"
                        placeholder="https://github.com/..."
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>
            </div>

            <!-- Technologies selection -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Technologies Tagged</label>
                <div class="flex flex-wrap gap-2">
                    <label
                        v-for="tech in technologies"
                        :key="tech.id"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-colors"
                        :class="form.technology_ids.includes(tech.id) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300'"
                    >
                        <input
                            type="checkbox"
                            :value="tech.id"
                            v-model="form.technology_ids"
                            class="hidden"
                        />
                        <span>{{ tech.name }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    id="is_featured"
                    v-model="form.is_featured"
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                />
                <label for="is_featured" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Feature on Homepage Showcase
                </label>
            </div>

            <div class="space-y-3">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Cover image</label>
                <div v-if="props.project?.cover_image" class="max-w-sm space-y-2">
                    <img
                        :src="`/storage/${props.project.cover_image}`"
                        alt="Current cover"
                        class="h-40 w-full rounded-xl border border-slate-200 object-cover dark:border-slate-800"
                    />
                    <p class="text-xs text-slate-500">{{ fileName(props.project.cover_image) }}</p>
                </div>
                <input type="file" accept="image/*" class="block text-sm" @change="form.cover_image = ($event.target as HTMLInputElement).files?.[0] ?? null" />
                <p v-if="form.errors.cover_image" class="text-xs text-red-600">{{ form.errors.cover_image }}</p>
            </div>

            <div class="space-y-3">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Screenshots</label>
                <input type="file" accept="image/*" multiple class="block text-sm" @change="form.images = Array.from(($event.target as HTMLInputElement).files ?? [])" />
                <div v-if="props.project?.images?.length" class="space-y-3">
                    <label v-for="image in props.project.images" :key="image.id" class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" :value="image.id" v-model="form.remove_image_ids" />
                        <img
                            :src="`/storage/${image.path}`"
                            :alt="fileName(image.path)"
                            class="h-16 w-24 rounded-lg border border-slate-200 object-cover dark:border-slate-800"
                        />
                        <span>{{ fileName(image.path) }}</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow transition-colors"
                >
                    <Save class="w-4 h-4" />
                    <span>{{ isEditing ? 'Update Project' : 'Save Project' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>

