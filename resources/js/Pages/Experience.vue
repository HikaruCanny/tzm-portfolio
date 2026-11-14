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
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <Head title="Experience & Education – TZM" />

        <div class="space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Experience & Education
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                A timeline of professional milestones, engineering roles, and educational foundation.
            </p>
        </div>

        <div v-if="props.experiences.length > 0" class="relative border-l-2 border-indigo-200 dark:border-indigo-950 ml-4 space-y-10 pl-6">
            <div
                v-for="item in props.experiences"
                :key="item.id"
                class="relative group"
            >
                <!-- Dot -->
                <div class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full bg-white dark:bg-slate-950 border-2 border-indigo-600 dark:border-indigo-400 group-hover:scale-125 transition-transform"></div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm hover:border-indigo-500/50 transition-colors space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300">
                            <Briefcase v-if="item.type === 'work'" class="w-3.5 h-3.5" />
                            <GraduationCap v-else-if="item.type === 'education'" class="w-3.5 h-3.5" />
                            <Award v-else class="w-3.5 h-3.5" />
                            <span>{{ item.role }}</span>
                        </span>
                        <span class="flex items-center gap-1 text-xs text-slate-500">
                            <Calendar class="w-3.5 h-3.5" />
                            <span>{{ item.start_date.substring(0, 7) }} – {{ item.end_date ? item.end_date.substring(0, 7) : 'Present' }}</span>
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ item.organization }}
                    </h3>

                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                        {{ item.description }}
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 text-slate-500 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p>No timeline milestones recorded yet.</p>
        </div>
    </div>
</template>
