<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Reveal from '@/components/Reveal.vue';
import { ArrowRight, Cloud, Code2, Cpu, Database, Layout, Sparkles, Wrench } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Skill {
    id: number;
    name: string;
    level?: number | null;
}

interface SkillCategory {
    id: number;
    name: string;
    skills: Skill[];
}

const props = withDefaults(defineProps<{
    categories?: SkillCategory[];
}>(), {
    categories: () => [],
});

const activeId = ref<number | null>(null);
const animated = ref(false);

const totalSkills = computed(() =>
    props.categories.reduce((sum, category) => sum + category.skills.length, 0),
);

const activeCategory = computed(() => {
    if (!props.categories.length) {
        return null;
    }

    return props.categories.find((category) => category.id === activeId.value) ?? props.categories[0];
});

const getCategoryIcon = (name: string) => {
    const lower = name.toLowerCase();

    if (lower.includes('backend') || lower.includes('api')) {
        return Cpu;
    }

    if (lower.includes('frontend') || lower.includes('ui')) {
        return Layout;
    }

    if (lower.includes('database') || lower.includes('data')) {
        return Database;
    }

    if (lower.includes('devops') || lower.includes('cloud') || lower.includes('infra')) {
        return Cloud;
    }

    if (lower.includes('tool')) {
        return Wrench;
    }

    if (lower.includes('language') || lower.includes('code')) {
        return Code2;
    }

    return Sparkles;
};

const skillTone = (index: number): string => `skill-tone-${index % 6}`;

const skillSurface = (index: number): string => `skill-surface-${index % 6}`;

const purpleIconTone = (index: number): string => `skill-tone-${index % 3}`;

const barTone = 'from-primary-300 to-primary-600 dark:from-primary-400 dark:to-primary-200';

const categoryIndex = (id: number): number =>
    props.categories.findIndex((item) => item.id === id);

onMounted(() => {
    if (props.categories.length) {
        activeId.value = props.categories[0].id;
    }

    requestAnimationFrame(() => {
        animated.value = true;
    });
});
</script>

<template>
    <div class="section-bg-a">
        <Head title="Skills & Technologies – TZM" />

        <div class="mx-auto max-w-6xl space-y-14 px-4 py-16 sm:px-6 lg:px-8">
            <Reveal>
                <div class="grid items-end gap-8 lg:grid-cols-[1.4fr_0.8fr]">
                    <div class="space-y-5">
                        <p class="text-special text-xs font-semibold uppercase tracking-[0.24em]">
                            Skills
                        </p>
                        <h1 class="max-w-2xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl dark:text-white">
                            Tools shaped by real product work
                        </h1>
                        <p class="max-w-xl text-lg leading-relaxed text-slate-600 dark:text-slate-300">
                            A clear map of the stack I use to design APIs, ship Vue interfaces, and keep systems reliable in production.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ props.categories.length }}</p>
                            <p class="mt-1 text-sm text-slate-500">Categories</p>
                        </div>
                        <div class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/70">
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalSkills }}</p>
                            <p class="mt-1 text-sm text-slate-500">Skills listed</p>
                        </div>
                    </div>
                </div>
            </Reveal>

            <div v-if="props.categories.length" class="space-y-8">
                <Reveal :delay="80">
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="(category, index) in props.categories"
                            :key="category.id"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-all"
                            :class="activeCategory?.id === category.id
                                ? 'bg-primary-600 text-white shadow-sm dark:bg-primary-300 dark:text-primary-950'
                                : 'chip-light border border-transparent hover:border-primary-200 dark:hover:border-white/15'"
                            @click="activeId = category.id"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full text-[11px]"
                                :class="activeCategory?.id === category.id ? 'bg-white/20 dark:bg-primary-950/20' : purpleIconTone(index)"
                            >
                                <component :is="getCategoryIcon(category.name)" class="h-3.5 w-3.5" />
                            </span>
                            {{ category.name }}
                        </button>
                    </div>
                </Reveal>

                <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                    <Reveal :delay="120">
                        <article
                            v-if="activeCategory"
                            class="rounded-[2rem] border p-7 shadow-sm"
                            :class="skillSurface(categoryIndex(activeCategory.id))"
                        >
                            <div class="space-y-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-3">
                                        <div
                                            :class="['inline-flex h-12 w-12 items-center justify-center rounded-2xl', purpleIconTone(categoryIndex(activeCategory.id))]"
                                        >
                                            <component :is="getCategoryIcon(activeCategory.name)" class="h-5 w-5" />
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-extrabold text-cyan-700 dark:text-cyan-300">
                                                {{ activeCategory.name }}
                                            </h2>
                                            <p class="mt-1 text-sm text-slate-500">
                                                {{ activeCategory.skills.length }} skills in this group
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div
                                        v-for="(skill, skillIndex) in activeCategory.skills"
                                        :key="skill.id"
                                        class="space-y-2"
                                    >
                                        <div class="flex items-center justify-between gap-3 text-sm">
                                            <span :class="['rounded-full px-2.5 py-0.5 text-xs font-semibold', skillTone(skillIndex)]">{{ skill.name }}</span>
                                            <span v-if="skill.level" class="text-xs font-medium text-slate-400">{{ skill.level }}%</span>
                                        </div>
                                        <div
                                            v-if="skill.level"
                                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
                                        >
                                            <div
                                                class="h-full rounded-full bg-gradient-to-r transition-all duration-700 ease-out"
                                                :class="barTone"
                                                :style="{ width: animated ? `${skill.level}%` : '0%' }"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </Reveal>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <Reveal
                            v-for="(category, index) in props.categories"
                            :key="category.id"
                            :delay="140 + index * 70"
                        >
                            <button
                                type="button"
                                class="group h-full rounded-[1.75rem] border p-5 text-left shadow-sm transition-all hover:-translate-y-1 hover:shadow-md"
                                :class="[
                                    skillSurface(index),
                                    activeCategory?.id === category.id ? 'ring-2 ring-primary-300/70 dark:ring-primary-400/35' : '',
                                ]"
                                @click="activeId = category.id"
                            >
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span
                                            :class="['inline-flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(index)]"
                                        >
                                            <component :is="getCategoryIcon(category.name)" class="h-4 w-4" />
                                        </span>
                                        <span class="text-xs font-semibold text-slate-400">{{ category.skills.length }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-cyan-700 dark:text-cyan-300">{{ category.name }}</h3>
                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            <span
                                                v-for="(skill, skillIndex) in category.skills.slice(0, 4)"
                                                :key="skill.id"
                                                :class="['rounded-full px-2.5 py-1 text-xs font-medium', skillTone(index * 3 + skillIndex)]"
                                            >
                                                {{ skill.name }}
                                            </span>
                                            <span
                                                v-if="category.skills.length > 4"
                                                class="rounded-full px-2.5 py-1 text-xs font-medium text-slate-400"
                                            >
                                                +{{ category.skills.length - 4 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </Reveal>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-6 py-16 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-900/50"
            >
                No skills recorded yet.
            </div>

            <Reveal :delay="180">
                <div class="flex flex-col items-start justify-between gap-4 rounded-[2rem] border border-primary-100 bg-gradient-to-r from-primary-50 via-white to-primary-50/40 px-6 py-6 sm:flex-row sm:items-center dark:border-slate-800 dark:from-primary-950/40 dark:via-slate-900 dark:to-primary-950/20">
                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Want to see these skills in shipped work?</p>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Browse project case studies or send a short note.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link
                            href="/projects"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-950"
                        >
                            <span>View projects</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link
                            href="/contact"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-white dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Contact
                        </Link>
                    </div>
                </div>
            </Reveal>
        </div>
    </div>
</template>
