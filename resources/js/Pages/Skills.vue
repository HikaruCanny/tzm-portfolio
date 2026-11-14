<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Cpu, Layout, Database, Wrench, Sparkles } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Skill {
    id: number;
    name: string;
    level?: number;
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

const getCategoryIcon = (name: string) => {
    const lower = name.toLowerCase();
    if (lower.includes('backend')) return Cpu;
    if (lower.includes('frontend')) return Layout;
    if (lower.includes('database')) return Database;
    if (lower.includes('devops') || lower.includes('tool')) return Wrench;
    return Sparkles;
};
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <Head title="Skills & Technologies – TZM" />

        <div class="space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Technical Skills
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Categorized breakdown of backend, frontend, database, and infrastructure competencies.
            </p>
        </div>

        <div v-if="props.categories.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div
                v-for="cat in props.categories"
                :key="cat.id"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <component :is="getCategoryIcon(cat.name)" class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ cat.name }}
                    </h2>
                </div>

                <div class="space-y-4">
                    <div v-for="skill in cat.skills" :key="skill.id" class="space-y-1.5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ skill.name }}</span>
                            <span v-if="skill.level" class="text-xs text-slate-400">{{ skill.level }}%</span>
                        </div>
                        <div v-if="skill.level" class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div
                                class="bg-indigo-600 dark:bg-indigo-500 h-2 rounded-full transition-all duration-500"
                                :style="{ width: `${skill.level}%` }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 text-slate-500 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p>No skills recorded yet.</p>
        </div>
    </div>
</template>
