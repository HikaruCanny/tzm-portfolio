<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Download, Target, UserRound, Waypoints } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Profile {
    name?: string;
    title?: string;
    tagline?: string;
    bio?: string | null;
    goals?: string | null;
    working_style?: string | null;
    email?: string;
    location?: string;
    photo_path?: string | null;
    resume_path?: string | null;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
}>(), {
    profile: () => ({
        name: 'TZM',
        title: 'Full-Stack Developer (Laravel + Vue.js)',
        bio: 'Full-stack software developer specialized in the Laravel and Vue.js ecosystem.',
        location: 'Available for Remote Roles',
    }),
});

const defaultGoals = 'Build reliable Laravel and Vue products, keep shipping strong case studies, and grow into roles where architecture, mentoring, and delivery quality matter.';
const defaultWorkingStyle = 'Clear written communication, small vertical slices, tests around critical paths, and UI that stays consistent with Tailwind design systems.';
</script>

<template>
    <div class="mx-auto max-w-5xl space-y-16 px-4 py-16 sm:px-6 lg:px-8">
        <Head :title="`About – ${props.profile.name || 'TZM'}`" />

        <div class="space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                About Me
            </h1>
            <p class="max-w-2xl text-lg text-slate-600 dark:text-slate-400">
                {{ props.profile.tagline || 'Software craftsman focused on maintainable systems, clean architectures, and responsive interfaces.' }}
            </p>
        </div>

        <div class="grid items-start gap-10 md:grid-cols-3">
            <div class="space-y-6 md:col-span-2">
                <section class="space-y-4">
                    <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-300">
                        <UserRound class="h-4 w-4" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em]">Story</h2>
                    </div>
                    <p class="whitespace-pre-line leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ props.profile.bio }}
                    </p>
                </section>

                <section class="space-y-4">
                    <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-300">
                        <Target class="h-4 w-4" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em]">Goals</h2>
                    </div>
                    <p class="whitespace-pre-line leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ props.profile.goals || defaultGoals }}
                    </p>
                </section>

                <section class="space-y-4">
                    <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-300">
                        <Waypoints class="h-4 w-4" />
                        <h2 class="text-sm font-semibold uppercase tracking-[0.18em]">Working style</h2>
                    </div>
                    <p class="whitespace-pre-line leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ props.profile.working_style || defaultWorkingStyle }}
                    </p>
                </section>

                <div class="flex flex-wrap gap-4 pt-2">
                    <Link
                        href="/contact"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 font-semibold text-white shadow-sm hover:bg-indigo-700"
                    >
                        <span>Get In Touch</span>
                    </Link>
                    <a
                        v-if="props.profile.resume_path"
                        href="/resume"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                    >
                        <Download class="h-4 w-4" />
                        <span>Download Resume (PDF)</span>
                    </a>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <img
                        v-if="props.profile.photo_path"
                        :src="`/storage/${props.profile.photo_path}`"
                        :alt="props.profile.name || 'Profile photo'"
                        class="aspect-[4/5] w-full object-cover"
                    />
                    <div
                        v-else
                        class="flex aspect-[4/5] w-full items-center justify-center bg-indigo-50 text-4xl font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"
                    >
                        {{ (props.profile.name || 'TZM').slice(0, 1) }}
                    </div>
                </div>

                <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Quick Facts</h3>
                    <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-500">Name</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ props.profile.name || 'TZM' }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-500">Role</span>
                            <span class="text-right text-slate-800 dark:text-slate-200">{{ props.profile.title || 'Full-Stack Developer' }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-500">Stack</span>
                            <span class="text-slate-800 dark:text-slate-200">Laravel + Vue 3</span>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-500">Location</span>
                            <span class="text-right text-xs text-slate-800 dark:text-slate-200">{{ props.profile.location || 'Remote' }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-500">Status</span>
                            <span class="font-medium text-emerald-600 dark:text-emerald-400">Open to opportunities</span>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</template>
