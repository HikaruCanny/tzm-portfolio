<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Download, FileText, User, Terminal, Award } from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Profile {
    name?: string;
    title?: string;
    tagline?: string;
    bio?: string;
    email?: string;
    location?: string;
    resume_path?: string;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
}>(), {
    profile: () => ({
        name: 'TZM',
        title: 'Full-Stack Developer (Laravel + Vue.js)',
        bio: 'I am a Full-Stack Web Developer specialized in the Laravel and Vue.js ecosystems. With hands-on experience structuring scalable APIs, managing relational databases, and designing intuitive frontends, I enjoy taking applications from concept through deployment.',
        location: 'Available for Remote Roles',
        email: 'contact@tzm.dev',
    }),
});
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <Head :title="`About – ${props.profile.name || 'TZM'}`" />

        <div class="space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                About Me
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Software craftsman focused on building maintainable systems, clean architectures, and responsive interfaces.
            </p>
        </div>

        <!-- Bio & Story -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-start">
            <div class="md:col-span-2 space-y-6 text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                <p>
                    {{ props.profile.bio }}
                </p>
                <p>
                    My workflow prioritizes strong code ergonomics: domain-driven data design, comprehensive test suites with Pest, clear API boundaries, and consistent UI design systems powered by Tailwind CSS.
                </p>

                <div class="pt-4 flex flex-wrap gap-4">
                    <Link
                        href="/contact"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors shadow-sm"
                    >
                        <span>Get In Touch</span>
                    </Link>
                    <a
                        v-if="props.profile.resume_path"
                        href="/resume"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        <Download class="w-4 h-4" />
                        <span>Download Resume (PDF)</span>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Quick Facts</h3>
                <ul class="text-sm space-y-3 text-slate-600 dark:text-slate-400">
                    <li class="flex items-center justify-between">
                        <span class="font-medium text-slate-500">Name:</span>
                        <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ props.profile.name || 'TZM' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="font-medium text-slate-500">Role:</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ props.profile.title || 'Full-Stack Developer' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="font-medium text-slate-500">Primary Stack:</span>
                        <span class="text-slate-800 dark:text-slate-200">Laravel + Vue 3</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="font-medium text-slate-500">Location:</span>
                        <span class="text-slate-800 dark:text-slate-200 text-xs">{{ props.profile.location || 'Remote' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="font-medium text-slate-500">Status:</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">Open to opportunities</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
