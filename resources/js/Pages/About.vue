<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Reveal from '@/components/Reveal.vue';
import {
    ArrowRight,
    Download,
    MapPin,
    Sparkles,
    Target,
    UserRound,
    Waypoints,
    Wrench,
} from '@lucide/vue';

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
    has_resume?: boolean;
    resume_url?: string | null;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
}>(), {
    profile: () => ({
        name: 'Thant Zin Moe',
        title: 'Full-Stack Developer (Laravel + Vue.js)',
        bio: 'I build full-stack web products with Laravel, Inertia, and Vue 3.',
        location: 'Myanmar · Open to global remote roles',
    }),
});

const defaultGoals = 'Build reliable Laravel and Vue products, keep shipping strong case studies, and grow into roles where architecture, mentoring, and delivery quality matter.';
const defaultWorkingStyle = 'Clear written communication, small vertical slices, tests around critical paths, and UI that stays consistent with Tailwind design systems.';

const sections = [
    {
        key: 'story',
        title: 'Story',
        icon: UserRound,
        chip: 'skill-tone-0',
        heading: 'text-purple-700 dark:text-purple-300',
        body: () => props.profile.bio,
    },
    {
        key: 'goals',
        title: 'Goals',
        icon: Target,
        chip: 'skill-tone-1',
        heading: 'text-violet-700 dark:text-violet-300',
        body: () => props.profile.goals || defaultGoals,
    },
    {
        key: 'working-style',
        title: 'Working style',
        icon: Waypoints,
        chip: 'skill-tone-2',
        heading: 'text-fuchsia-700 dark:text-fuchsia-300',
        body: () => props.profile.working_style || defaultWorkingStyle,
    },
] as const;

const initials = (props.profile.name || 'TZM').slice(0, 1).toUpperCase();
</script>

<template>
    <div class="section-bg-b">
        <Head :title="`About – ${props.profile.name || 'TZM'}`" />

        <div class="mx-auto max-w-6xl space-y-14 px-4 py-16 sm:px-6 lg:px-8">
            <Reveal>
                <header class="max-w-3xl space-y-5">
                    <p class="text-special text-xs font-semibold uppercase tracking-[0.24em]">
                        About
                    </p>
                    <h1 class="text-4xl font-extrabold tracking-tight text-violet-700 sm:text-5xl dark:text-violet-300">
                        {{ props.profile.name || 'TZM' }}
                    </h1>
                    <p class="skill-tone-0 inline-flex rounded-lg px-3 py-1.5 text-lg font-medium">
                        {{ props.profile.title || 'Full-Stack Developer' }}
                    </p>
                    <p class="text-lg leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ props.profile.tagline || 'Software craftsman focused on maintainable systems, clean architectures, and responsive interfaces.' }}
                    </p>
                </header>
            </Reveal>

            <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-12">
                <div class="space-y-6 lg:col-span-7">
                    <Reveal
                        v-for="(section, index) in sections"
                        :key="section.key"
                        :delay="index * 80"
                    >
                        <article class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5 sm:p-7">
                            <div class="flex items-start gap-4">
                                <div :class="['flex h-11 w-11 shrink-0 items-center justify-center rounded-xl', section.chip]">
                                    <component :is="section.icon" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0 space-y-3">
                                    <h2 :class="['text-sm font-bold uppercase tracking-[0.2em]', section.heading]">
                                        {{ section.title }}
                                    </h2>
                                    <p class="whitespace-pre-line text-base leading-relaxed text-slate-600 dark:text-slate-300">
                                        {{ section.body() }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </Reveal>

                    <Reveal :delay="260">
                        <div class="flex flex-wrap gap-3 pt-2">
                            <Link
                                href="/contact"
                                class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500 dark:bg-primary-300 dark:text-primary-950 dark:hover:bg-primary-200"
                            >
                                <span>Get in touch</span>
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <a
                                v-if="props.profile.has_resume"
                                :href="props.profile.resume_url ?? '/resume'"
                                download
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-primary-300 hover:bg-white dark:border-slate-700 dark:text-slate-200 dark:hover:border-white/20 dark:hover:bg-white/5"
                            >
                                <Download class="h-4 w-4" />
                                <span>Resume</span>
                            </a>
                        </div>
                    </Reveal>
                </div>

                <aside class="space-y-5 lg:col-span-5 lg:sticky lg:top-24">
                    <Reveal :delay="120">
                        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                            <img
                                v-if="props.profile.photo_path"
                                :src="`/storage/${props.profile.photo_path}`"
                                :alt="props.profile.name || 'Profile photo'"
                                class="aspect-[4/5] w-full object-cover"
                            />
                            <div
                                v-else
                                class="flex aspect-[4/5] w-full items-center justify-center bg-primary-600 text-6xl font-extrabold text-white dark:bg-primary-500/90"
                            >
                                {{ initials }}
                            </div>
                        </div>
                    </Reveal>

                    <Reveal :delay="180">
                        <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <div class="skill-tone-3 flex h-10 w-10 items-center justify-center rounded-xl">
                                    <Sparkles class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-cyan-700 dark:text-cyan-300">
                                        Quick facts
                                    </h3>
                                    <p class="text-xs text-slate-500">At a glance</p>
                                </div>
                            </div>

                            <ul class="mt-5 space-y-3 text-sm">
                                <li class="flex items-start gap-3 rounded-xl border border-slate-100 px-3 py-2.5 dark:border-white/10">
                                    <UserRound class="mt-0.5 h-4 w-4 shrink-0 text-purple-600 dark:text-purple-300" />
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Name</p>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ props.profile.name || 'TZM' }}</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3 rounded-xl border border-slate-100 px-3 py-2.5 dark:border-white/10">
                                    <Target class="mt-0.5 h-4 w-4 shrink-0 text-violet-600 dark:text-violet-300" />
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Role</p>
                                        <p class="text-slate-800 dark:text-slate-200">{{ props.profile.title || 'Full-Stack Developer' }}</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3 rounded-xl border border-slate-100 px-3 py-2.5 dark:border-white/10">
                                    <Wrench class="mt-0.5 h-4 w-4 shrink-0 text-fuchsia-600 dark:text-fuchsia-300" />
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Stack</p>
                                        <p class="text-slate-800 dark:text-slate-200">Laravel + Vue 3</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3 rounded-xl border border-slate-100 px-3 py-2.5 dark:border-white/10">
                                    <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-cyan-600 dark:text-cyan-300" />
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Location</p>
                                        <p class="text-slate-800 dark:text-slate-200">{{ props.profile.location || 'Remote' }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </Reveal>
                </aside>
            </div>
        </div>
    </div>
</template>