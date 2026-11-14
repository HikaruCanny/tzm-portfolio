<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import { 
    FolderKanban, 
    ExternalLink, 
    Search,
    ArrowRight
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Technology {
    id: number;
    name: string;
}

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    role?: string;
    cover_image?: string | null;
    demo_url?: string;
    repo_url?: string;
    is_featured: boolean;
    published_at?: string | null;
    technologies: Technology[];
}

const props = withDefaults(defineProps<{
    projects?: Project[];
    technologies?: string[];
}>(), {
    projects: () => [],
    technologies: () => [],
});

const searchQuery = ref('');
const selectedTechnology = ref('All');
const sortBy = ref<'newest' | 'featured'>('newest');
const page = ref(1);
const perPage = 6;

const availableFilterOptions = computed(() => {
    return ['All', ...props.technologies];
});

const filteredProjects = computed(() => {
    const matches = props.projects.filter((project) => {
        const matchesTech =
            selectedTechnology.value === 'All' ||
            project.technologies.some(t => t.name.toLowerCase() === selectedTechnology.value.toLowerCase());

        const matchesSearch =
            project.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            project.summary.toLowerCase().includes(searchQuery.value.toLowerCase());

        return matchesTech && matchesSearch;
    });

    return [...matches].sort((left, right) => {
        if (sortBy.value === 'featured' && left.is_featured !== right.is_featured) {
            return left.is_featured ? -1 : 1;
        }

        return new Date(right.published_at ?? 0).getTime() - new Date(left.published_at ?? 0).getTime();
    });
});

const pageCount = computed(() => Math.max(1, Math.ceil(filteredProjects.value.length / perPage)));

const visibleProjects = computed(() => {
    const start = (page.value - 1) * perPage;

    return filteredProjects.value.slice(start, start + perPage);
});

watch([searchQuery, selectedTechnology, sortBy], () => {
    page.value = 1;
});

watch(pageCount, (count) => {
    if (page.value > count) {
        page.value = count;
    }
});

const showPage = (next: number) => {
    page.value = Math.min(pageCount.value, Math.max(1, next));
};
</script>

<template>
    <div class="mx-auto max-w-7xl space-y-12 px-4 py-16 sm:px-6 lg:px-8">
        <Head title="Projects Portfolio – TZM" />

        <div class="space-y-4 max-w-2xl">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Projects
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Explore real case studies, code repositories, and production-ready applications.
            </p>
        </div>

        <!-- Filter & Search Controls -->
        <div class="flex flex-col items-stretch justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tech in availableFilterOptions"
                    :key="tech"
                    @click="selectedTechnology = tech"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors',
                        selectedTechnology === tech
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'border border-indigo-100 bg-white text-slate-600 hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800'
                    ]"
                >
                    {{ tech }}
                </button>
            </div>

            <!-- Search input -->
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <label class="sr-only" for="project-sort">Sort projects</label>
                <select
                    id="project-sort"
                    v-model="sortBy"
                    class="rounded-lg border border-indigo-100 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                >
                    <option value="newest">Newest</option>
                    <option value="featured">Featured first</option>
                </select>
                <div class="relative w-full sm:w-72">
                <Search class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search projects..."
                    class="w-full rounded-lg border border-indigo-100 bg-white py-2 pl-9 pr-4 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                />
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div v-if="visibleProjects.length > 0" class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <div
                v-for="project in visibleProjects"
                :key="project.id"
                class="flex flex-col justify-between overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm transition-all hover:border-indigo-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
            >
                <Link
                    v-if="project.cover_image"
                    :href="`/projects/${project.slug}`"
                    class="relative block aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800"
                >
                    <img
                        :src="`/storage/${project.cover_image}`"
                        :alt="`${project.title} cover`"
                        class="h-full w-full object-cover"
                    />
                    <span
                        v-if="project.is_featured"
                        class="absolute top-3 right-3 text-xs px-2.5 py-0.5 rounded-full font-semibold bg-white/95 text-indigo-700"
                    >
                        Featured
                    </span>
                </Link>
                <div class="p-6 space-y-4">
                    <div v-if="!project.cover_image" class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <FolderKanban class="w-5 h-5" />
                        </div>
                        <span
                            v-if="project.is_featured"
                            class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                        >
                            Featured
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white hover:text-indigo-600 transition-colors">
                        <Link :href="`/projects/${project.slug}`">
                            {{ project.title }}
                        </Link>
                    </h3>

                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-3">
                        {{ project.summary }}
                    </p>
                </div>

                <div class="px-6 pb-6 pt-6 border-t border-slate-100 dark:border-slate-800/80 space-y-4">
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="tag in project.technologies"
                            :key="tag.id"
                            class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium"
                        >
                            {{ tag.name }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm font-semibold pt-1">
                        <div class="flex items-center gap-3">
                            <a
                                v-if="project.repo_url"
                                :href="project.repo_url"
                                target="_blank"
                                class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 inline-flex items-center gap-1.5 transition-colors"
                                title="Repository"
                            >
                                <GithubIcon class="w-4 h-4" />
                            </a>
                            <a
                                v-if="project.demo_url"
                                :href="project.demo_url"
                                target="_blank"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1.5"
                                title="Live Demo"
                            >
                                <ExternalLink class="w-4 h-4" />
                            </a>
                        </div>

                        <Link
                            :href="`/projects/${project.slug}`"
                            class="text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1 text-xs"
                        >
                            <span>Case study</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-2xl border border-indigo-100 bg-white py-16 text-center dark:border-slate-800 dark:bg-slate-900">
            <p class="text-slate-500 dark:text-slate-400">No projects match the current filter or search criteria.</p>
        </div>

        <div v-if="pageCount > 1" class="flex items-center justify-between gap-3">
            <button type="button" class="rounded-full border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 disabled:border-slate-200 disabled:text-slate-300 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 dark:disabled:text-slate-600" :disabled="page === 1" @click="showPage(page - 1)">
                Previous
            </button>
            <span class="text-center text-xs font-semibold text-slate-400">
                {{ (page - 1) * perPage + 1 }}–{{ Math.min(page * perPage, filteredProjects.length) }} of {{ filteredProjects.length }}
            </span>
            <button type="button" class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:bg-slate-300 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-100 dark:disabled:bg-slate-700" :disabled="page === pageCount" @click="showPage(page + 1)">
                Next
            </button>
        </div>
    </div>
</template>
