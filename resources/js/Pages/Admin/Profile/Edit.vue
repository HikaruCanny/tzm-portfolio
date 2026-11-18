<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Save, User, Shield } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    profile: any;
}>();

const form = useForm({
    name: props.profile.name || '',
    title: props.profile.title || '',
    tagline: props.profile.tagline || '',
    bio: props.profile.bio || '',
    goals: props.profile.goals || '',
    working_style: props.profile.working_style || '',
    email: props.profile.email || '',
    github_url: props.profile.github_url || '',
    linkedin_url: props.profile.linkedin_url || '',
    location: props.profile.location || '',
    photo: null as File | null,
    resume: null as File | null,
});

const submit = () => {
    form.post('/admin/profile', { forceFormData: true });
};

const fileName = (path: string): string => path.split('/').pop() ?? path;
</script>

<template>
    <div class="max-w-4xl mx-auto space-y-6">
        <Head title="Site Profile – TZM Admin" />

        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600">
                <Shield class="w-5 h-5" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Site Identity & Profile (Superadmin)
                </h1>
                <p class="text-sm text-slate-500">
                    Update the central details, pitch, and social links that power your public website.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Display Name</label>
                    <input v-model="form.name" required type="text" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Professional Title</label>
                    <input v-model="form.title" required type="text" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">One-Line Hero Pitch</label>
                <input v-model="form.tagline" type="text" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Biography / About Story</label>
                <textarea v-model="form.bio" rows="4" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Goals</label>
                    <textarea v-model="form.goals" rows="4" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" placeholder="What you are aiming for next"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Working Style</label>
                    <textarea v-model="form.working_style" rows="4" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" placeholder="How you collaborate and ship"></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Public Contact Email</label>
                    <input v-model="form.email" type="email" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">GitHub Profile URL</label>
                    <input v-model="form.github_url" type="url" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">LinkedIn Profile URL</label>
                    <input v-model="form.linkedin_url" type="url" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Location Note</label>
                <input v-model="form.location" type="text" placeholder="E.g. Available for Global Remote Opportunities" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Photo</label>
                    <div v-if="props.profile.photo_path" class="mt-2 flex items-center gap-3">
                        <img
                            :src="`/storage/${props.profile.photo_path}`"
                            alt="Current site photo"
                            class="h-16 w-12 rounded-lg object-cover border border-slate-200 dark:border-slate-700"
                        />
                        <p class="text-xs text-slate-500 break-all">{{ fileName(props.profile.photo_path) }}</p>
                    </div>
                    <input type="file" accept="image/*" class="w-full mt-2 text-sm" @change="form.photo = ($event.target as HTMLInputElement).files?.[0] ?? null" />
                    <p v-if="form.errors.photo" class="mt-1 text-xs text-red-600">{{ form.errors.photo }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Resume PDF</label>
                    <p v-if="props.profile.resume_path" class="mt-2 text-xs text-slate-500 break-all">
                        {{ fileName(props.profile.resume_path) }}
                    </p>
                    <input type="file" accept="application/pdf" class="w-full mt-2 text-sm" @change="form.resume = ($event.target as HTMLInputElement).files?.[0] ?? null" />
                    <p v-if="form.errors.resume" class="mt-1 text-xs text-red-600">{{ form.errors.resume }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow transition-colors"
                >
                    <Save class="w-4 h-4" />
                    <span>Save Profile Changes</span>
                </button>
            </div>
        </form>
    </div>
</template>

