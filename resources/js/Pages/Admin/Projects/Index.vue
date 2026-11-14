<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Plus, Edit, Trash2, ExternalLink } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    projects: {
        data: any[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const deleteProject = (id: number) => {
    if (confirm('Are you sure you want to delete this project?')) {
        router.delete(`/admin/projects/${id}`);
    }
};
</script>

<template>
    <div class="space-y-6 max-w-7xl mx-auto">
        <Head title="Projects – TZM Admin" />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Projects Management
                </h1>
                <p class="text-sm text-slate-500">
                    Create, edit, and organize portfolio projects and case studies.
                </p>
            </div>

            <Link
                href="/admin/projects/create"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow hover:bg-indigo-700 transition-colors"
            >
                <Plus class="w-4 h-4" />
                <span>New Project</span>
            </Link>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-950/50 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Technologies</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Featured</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr v-for="proj in projects.data" :key="proj.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                            {{ proj.title }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="tech in proj.technologies"
                                    :key="tech.id"
                                    class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium"
                                >
                                    {{ tech.name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span 
                                class="text-xs px-2.5 py-0.5 rounded-full font-medium"
                                :class="proj.status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400'"
                            >
                                {{ proj.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold">
                            <span v-if="proj.is_featured" class="text-indigo-600 dark:text-indigo-400">Yes</span>
                            <span v-else class="text-slate-400">No</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link
                                :href="`/admin/projects/${proj.id}/edit`"
                                class="p-1.5 text-slate-500 hover:text-indigo-600 inline-block"
                                title="Edit"
                            >
                                <Edit class="w-4 h-4" />
                            </Link>
                            <button
                                @click="deleteProject(proj.id)"
                                type="button"
                                class="p-1.5 text-slate-500 hover:text-red-600 inline-block"
                                title="Delete"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :paginator="projects" />
    </div>
</template>

