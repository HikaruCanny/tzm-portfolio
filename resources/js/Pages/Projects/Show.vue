<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import { 
    ArrowLeft, 
    ExternalLink, 
    FolderKanban, 
    Layers, 
    CheckCircle2, 
    Calendar,
    ArrowRight
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Technology {
    id: number;
    name: string;
}

interface ProjectImage {
    id: number;
    path: string;
    caption?: string;
}

interface Project {
    id: number;
    title: string;
    slug: string;
    summary?: string;
    problem?: string;
    solution?: string;
    role?: string;
    cover_image?: string | null;
    cover_url: string;
    demo_url?: string;
    repo_url?: string;
    published_at?: string;
    technologies: Technology[];
    images?: ProjectImage[];
}

const props = defineProps<{
    project: Project;
    relatedProjects?: Project[];
}>();

const activeImage = ref<ProjectImage | null>(null);

function closeOnEscape(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        activeImage.value = null;
    }
}

onMounted(() => window.addEventListener('keydown', closeOnEscape));
onBeforeUnmount(() => window.removeEventListener('keydown', closeOnEscape));

const skillTone = (index: number): string => `skill-tone-${index % 6}`;
const purpleIconTone = (index: number): string => `skill-tone-${index % 3}`;
const titleCyan = 'text-cyan-700 transition-colors hover:text-cyan-800 dark:text-cyan-300 dark:hover:text-cyan-200';
const actionLightPurple = 'skill-tone-0';
</script>

<template>
    <div class="section-bg-b">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <Head :title="`${props.project.title} – Case Study – TZM`" />

        <!-- Back Button -->
        <div>
            <Link
                href="/projects"
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Back to all projects</span>
            </Link>
        </div>

        <!-- Header -->
        <header class="space-y-6">
            <div class="flex flex-wrap items-center gap-2">
                <span
                    v-for="(tech, techIndex) in props.project.technologies"
                    :key="tech.id"
                    :class="['rounded-full px-3 py-1 text-xs font-semibold', skillTone(techIndex)]"
                >
                    {{ tech.name }}
                </span>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-cyan-700 sm:text-5xl dark:text-cyan-300">
                {{ props.project.title }}
            </h1>

            <p class="text-xl text-slate-600 dark:text-slate-300 leading-relaxed">
                {{ props.project.summary }}
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a
                    v-if="props.project.demo_url"
                    :href="props.project.demo_url"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-600 text-white font-semibold shadow hover:bg-primary-700 transition-colors"
                >
                    <ExternalLink class="w-4 h-4" />
                    <span>View Live Demo</span>
                </a>

                <a
                    v-if="props.project.repo_url"
                    :href="props.project.repo_url"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                    <GithubIcon class="w-4 h-4" />
                    <span>Source Code</span>
                </a>
            </div>
        </header>

        <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-900">
            <img
                :src="props.project.cover_url"
                :alt="`${props.project.title} cover`"
                class="h-auto max-h-[32rem] w-full object-cover"
            />
        </figure>

        <!-- Project Meta Strip -->
        <div class="grid grid-cols-2 gap-4 rounded-2xl border border-primary-100 bg-primary-50 p-6 text-sm dark:border-white/10 dark:bg-white/5 sm:grid-cols-4">
            <div>
                <span class="block text-xs text-slate-500 uppercase tracking-wider">Role</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ props.project.role || 'Full-Stack Developer' }}</span>
            </div>
            <div>
                <span class="block text-xs text-slate-500 uppercase tracking-wider">Timeline</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">
                    {{ props.project.published_at ? new Date(props.project.published_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short' }) : 'Production' }}
                </span>
            </div>
            <div>
                <span class="block text-xs text-slate-500 uppercase tracking-wider">Architecture</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Monolith / SPA</span>
            </div>
            <div>
                <span class="block text-xs text-slate-500 uppercase tracking-wider">Stack</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Laravel & Vue</span>
            </div>
        </div>

        <!-- Case Study Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Problem -->
            <div class="space-y-4 rounded-2xl border border-primary-100 bg-primary-50 p-8 shadow-sm dark:border-white/10 dark:bg-white/5">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>The Challenge & Problem</span>
                </h2>
                <div class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line text-sm sm:text-base">
                    {{ props.project.problem || 'This system was commissioned to solve high-concurrency request routing and clean administrative visibility without relying on fragmented micro-tools.' }}
                </div>
            </div>

            <!-- Solution -->
            <div class="space-y-4 rounded-2xl border border-primary-100 bg-primary-50 p-8 shadow-sm dark:border-white/10 dark:bg-white/5">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>The Solution & Architecture</span>
                </h2>
                <div class="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line text-sm sm:text-base">
                    {{ props.project.solution || 'Engineered clean domain Eloquent entities, asynchronous event listeners, and Vue 3 reactive dashboards to guarantee zero data loss and instantaneous UI feedback.' }}
                </div>
            </div>
        </div>

        <div v-if="props.project.images && props.project.images.length > 0" class="space-y-4">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Screenshots</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <button
                    v-for="image in props.project.images"
                    :key="image.id"
                    type="button"
                    class="group overflow-hidden rounded-xl border border-slate-200 bg-slate-100 text-left dark:border-slate-800 dark:bg-slate-900"
                    @click="activeImage = image"
                >
                    <img
                        :src="`/storage/${image.path}`"
                        :alt="image.caption || props.project.title"
                        class="aspect-[4/3] w-full object-cover object-top transition duration-300 group-hover:scale-[1.03]"
                    />
                    <span v-if="image.caption" class="block truncate px-3 py-2 text-xs text-slate-500">{{ image.caption }}</span>
                </button>
            </div>

            <div
                v-if="activeImage"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4"
                @click.self="activeImage = null"
            >
                <button
                    type="button"
                    class="absolute right-4 top-4 rounded-full bg-white/10 px-3 py-1 text-sm font-semibold text-white hover:bg-white/20"
                    @click="activeImage = null"
                >
                    Close
                </button>
                <figure class="w-full max-w-3xl">
                    <img
                        :src="`/storage/${activeImage.path}`"
                        :alt="activeImage.caption || props.project.title"
                        class="max-h-[75vh] w-full rounded-xl object-contain"
                    />
                    <figcaption v-if="activeImage.caption" class="mt-3 text-center text-sm text-slate-200">
                        {{ activeImage.caption }}
                    </figcaption>
                </figure>
            </div>
        </div>

        <!-- Related Projects -->
        <div v-if="props.relatedProjects && props.relatedProjects.length > 0" class="pt-12 border-t border-slate-200 dark:border-slate-800 space-y-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Other Case Studies</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <Link
                    v-for="(rel, index) in props.relatedProjects"
                    :key="rel.id"
                    :href="`/projects/${rel.slug}`"
                    class="block space-y-3 rounded-2xl border border-primary-100 bg-primary-50 p-5 transition-all hover:border-primary-500/50 hover:shadow-sm dark:border-white/10 dark:bg-white/5"
                >
                    <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(index)]">
                        <FolderKanban class="h-5 w-5" />
                    </div>
                    <h3 :class="['text-base font-bold', titleCyan]">
                        {{ rel.title }}
                    </h3>
                    <p class="line-clamp-2 text-xs text-slate-500">{{ rel.summary }}</p>
                    <span :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold', actionLightPurple]">
                        Case study
                        <ArrowRight class="h-3.5 w-3.5" />
                    </span>
                </Link>
            </div>
        </div>
    </div>
    </div>
</template>

