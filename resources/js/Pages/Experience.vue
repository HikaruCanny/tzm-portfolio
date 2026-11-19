<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Briefcase, GraduationCap, Award, Calendar } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface ExperienceItem {
    id: number;
    type: 'work' | 'education' | 'certification';
    organization: string;
    role: string;
    start_date: string;
    end_date?: string | null;
    description?: string;
}

const props = withDefaults(defineProps<{
    experiences?: ExperienceItem[];
}>(), {
    experiences: () => [],
});

const skillTone = (index: number): string => `skill-tone-${index % 6}`;

const period = (item: ExperienceItem): string => {
    const start = item.start_date.substring(0, 7);
    const end = item.end_date ? item.end_date.substring(0, 7) : 'Present';

    return `${start} – ${end}`;
};
</script>

<template>
    <div class="section-bg-a">
    <div class="mx-auto max-w-4xl space-y-12 px-4 py-16 sm:px-6 lg:px-8">
        <Head title="Experience & Education – TZM" />

        <div class="space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Experience & Education
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                A timeline of professional milestones, engineering roles, and educational foundation.
            </p>
        </div>

        <div v-if="props.experiences.length > 0" class="relative">
            <div
                aria-hidden="true"
                class="absolute bottom-0 left-6 top-0 w-0.5 -translate-x-1/2 rounded-full bg-gradient-to-b from-primary-600 to-primary-300 dark:from-primary-400 dark:to-primary-200"
            />

            <div class="space-y-8">
                <article
                    v-for="(item, experienceIndex) in props.experiences"
                    :key="item.id"
                    class="relative flex gap-6 sm:gap-8"
                >
                    <div class="relative z-10 flex w-12 shrink-0 justify-center pt-6">
                        <div
                            aria-hidden="true"
                            class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-primary-600 bg-white shadow-sm dark:border-primary-400 dark:bg-slate-950"
                        >
                            <span class="h-2 w-2 rounded-full bg-primary-600 dark:bg-primary-400" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-primary-500/40 sm:p-6">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span :class="['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold uppercase tracking-wide', skillTone(experienceIndex)]">
                                <Briefcase v-if="item.type === 'work'" class="h-3.5 w-3.5" />
                                <GraduationCap v-else-if="item.type === 'education'" class="h-3.5 w-3.5" />
                                <Award v-else class="h-3.5 w-3.5" />
                                <span>{{ item.role }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ period(item) }}
                            </span>
                        </div>

                        <h3 class="mt-3 text-lg font-bold text-slate-900 dark:text-white">
                            {{ item.organization }}
                        </h3>

                        <p
                            v-if="item.description"
                            class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-400"
                        >
                            {{ item.description }}
                        </p>
                    </div>
                </article>
            </div>
        </div>

        <div
            v-else
            class="rounded-2xl border border-slate-200 bg-white py-12 text-center text-slate-500 dark:border-slate-800 dark:bg-slate-900"
        >
            <p>No timeline milestones recorded yet.</p>
        </div>
    </div>
    </div>
</template>