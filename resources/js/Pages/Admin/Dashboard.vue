<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    FolderKanban, 
    Sparkles, 
    Briefcase, 
    Mail, 
    ArrowRight, 
    Plus,
    CheckCircle,
    Clock
} from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    stats: {
        projects_count: number;
        published_projects_count: number;
        skills_count: number;
        experiences_count: number;
        services_count: number;
        unread_messages_count: number;
        total_messages_count: number;
    };
    recentProjects: any[];
    recentMessages: any[];
}>();
</script>

<template>
    <div class="space-y-8 max-w-7xl mx-auto">
        <Head title="Admin Dashboard – TZM" />

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    System Overview
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Welcome back. Here is the operational summary of your portfolio content.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <Link
                    href="/admin/projects/create"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow hover:bg-indigo-700 transition-colors"
                >
                    <Plus class="w-4 h-4" />
                    <span>New Project</span>
                </Link>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Projects</span>
                    <FolderKanban class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ stats.projects_count }}
                </div>
                <p class="text-xs text-slate-500">
                    {{ stats.published_projects_count }} published
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Unread Messages</span>
                    <Mail class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ stats.unread_messages_count }}
                </div>
                <p class="text-xs text-slate-500">
                    {{ stats.total_messages_count }} total received
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Skills Catalog</span>
                    <Sparkles class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ stats.skills_count }}
                </div>
                <p class="text-xs text-slate-500">active competencies</p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Timeline Entries</span>
                    <Briefcase class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ stats.experiences_count }}
                </div>
                <p class="text-xs text-slate-500">career & education nodes</p>
            </div>
        </div>

        <!-- Recent Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Projects -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="font-bold text-slate-900 dark:text-white text-base">Recent Projects</h2>
                    <Link href="/admin/projects" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all
                    </Link>
                </div>

                <div v-if="recentProjects.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="proj in recentProjects" :key="proj.id" class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-slate-200">{{ proj.title }}</p>
                            <p class="text-xs text-slate-400">{{ proj.role || 'Full-Stack' }}</p>
                        </div>
                        <span 
                            class="text-xs px-2.5 py-0.5 rounded-full font-medium"
                            :class="proj.status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400'"
                        >
                            {{ proj.status }}
                        </span>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-sm text-slate-500">
                    No projects found.
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="font-bold text-slate-900 dark:text-white text-base">Visitor Inquiries</h2>
                    <Link href="/admin/messages" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View inbox
                    </Link>
                </div>

                <div v-if="recentMessages.length > 0" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div v-for="msg in recentMessages" :key="msg.id" class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-slate-800 dark:text-slate-200">{{ msg.name }}</p>
                            <p class="text-xs text-slate-400">{{ msg.subject || msg.email }}</p>
                        </div>
                        <span 
                            class="text-xs px-2.5 py-0.5 rounded-full font-medium"
                            :class="msg.read_at ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-semibold'"
                        >
                            {{ msg.read_at ? 'Read' : 'New' }}
                        </span>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-sm text-slate-500">
                    No visitor messages yet.
                </div>
            </div>
        </div>
    </div>
</template>

