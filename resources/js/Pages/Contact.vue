<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import { 
    Mail, 
    Send, 
    MapPin, 
    CheckCircle2,
    AlertCircle
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Profile {
    name?: string;
    email?: string;
    github_url?: string;
    linkedin_url?: string;
    location?: string;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
}>(), {
    profile: () => ({
        email: 'contact@tzm.dev',
        location: 'Available for Remote Roles',
        github_url: 'https://github.com',
        linkedin_url: 'https://linkedin.com',
    }),
});

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    _hp: '', // Honeypot field for anti-spam
});

const isSubmitted = ref(false);

const submitContact = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitted.value = true;
            form.reset();
        },
    });
};
</script>

<template>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <Head title="Contact – TZM" />

        <div class="space-y-4 max-w-2xl">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Get In Touch
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Interested in working together, discussing software architecture, or hiring for a role? Send a message directly.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-start">
            <!-- Contact Information -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg">Contact Channels</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                            <Mail class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Direct Email</p>
                                <a :href="`mailto:${props.profile.email || 'contact@tzm.dev'}`" class="hover:underline text-indigo-600 dark:text-indigo-400">
                                    {{ props.profile.email || 'contact@tzm.dev' }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                            <MapPin class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Location & Timezone</p>
                                <p>{{ props.profile.location || 'Available for Remote Roles' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-3">
                        <a
                            v-if="props.profile.github_url"
                            :href="props.profile.github_url"
                            target="_blank"
                            class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="GitHub"
                        >
                            <GithubIcon class="w-5 h-5" />
                        </a>
                        <a
                            v-if="props.profile.linkedin_url"
                            :href="props.profile.linkedin_url"
                            target="_blank"
                            class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="LinkedIn"
                        >
                            <LinkedinIcon class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 shadow-sm">
                <!-- Success Message -->
                <div v-if="isSubmitted" class="py-12 text-center space-y-4">
                    <CheckCircle2 class="w-14 h-14 text-emerald-500 mx-auto" />
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Message Dispatched!</h3>
                    <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                        Thank you for reaching out. Your message has been saved in the system and I'll respond as soon as possible.
                    </p>
                    <button
                        @click="isSubmitted = false"
                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700"
                    >
                        Send Another Note
                    </button>
                </div>

                <form v-else @submit.prevent="submitContact" class="space-y-6">
                    <!-- Honeypot (hidden from human visitors) -->
                    <input type="text" name="_hp" v-model="form._hp" class="hidden" tabindex="-1" autocomplete="off" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Your Name</label>
                            <input
                                v-model="form.name"
                                required
                                type="text"
                                placeholder="Jane Doe"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                            <p v-if="form.errors.name" class="text-xs text-rose-500">{{ form.errors.name }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Email Address</label>
                            <input
                                v-model="form.email"
                                required
                                type="email"
                                placeholder="jane@example.com"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                            <p v-if="form.errors.email" class="text-xs text-rose-500">{{ form.errors.email }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Subject</label>
                        <input
                            v-model="form.subject"
                            type="text"
                            placeholder="Full-Stack Engineering Opportunity / Project Discussion"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        <p v-if="form.errors.subject" class="text-xs text-rose-500">{{ form.errors.subject }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Message</label>
                        <textarea
                            v-model="form.message"
                            required
                            rows="5"
                            placeholder="Share some details about your team, project requirements, or timeline..."
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                        <p v-if="form.errors.message" class="text-xs text-rose-500">{{ form.errors.message }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full sm:w-auto px-8 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors shadow-md flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <Send class="w-4 h-4" />
                        <span>{{ form.processing ? 'Dispatching...' : 'Send Message' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
