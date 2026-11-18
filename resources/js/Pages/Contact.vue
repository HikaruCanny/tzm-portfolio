<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import {
    ChevronDown,
    CheckCircle2,
    Download,
    Mail,
    MapPin,
    Send,
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Profile {
    name?: string;
    email?: string;
    github_url?: string;
    linkedin_url?: string;
    location?: string;
    resume_path?: string | null;
}

interface Faq {
    question: string;
    answer: string;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
    faqs?: Faq[];
}>(), {
    profile: () => ({
        email: 'contact@tzm.dev',
        location: 'Available for Remote Roles',
    }),
    faqs: () => [],
});

const faqItems = computed(() => props.faqs ?? []);

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    _hp: '',
});

const isSubmitted = ref(false);
const openFaq = ref<number | null>(0);

const submitContact = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitted.value = true;
            form.reset();
        },
    });
};

const toggleFaq = (index: number) => {
    openFaq.value = openFaq.value === index ? null : index;
};
</script>

<template>
    <div id="faq" class="mx-auto max-w-6xl scroll-mt-24 space-y-12 px-4 py-16 sm:px-6 lg:px-8">
        <Head title="Contact – TZM" />

        <div class="max-w-2xl space-y-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Get In Touch
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Interested in working together, discussing software architecture, or hiring for a role? Send a message directly.
            </p>
        </div>

        <div class="grid items-start gap-12 lg:grid-cols-3">
            <div class="space-y-6">
                <div class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Contact Channels</h3>

                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                            <Mail class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400" />
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Direct Email</p>
                                <a
                                    :href="`mailto:${props.profile.email || 'contact@tzm.dev'}`"
                                    class="text-indigo-600 hover:underline dark:text-indigo-400"
                                >
                                    {{ props.profile.email || 'contact@tzm.dev' }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 text-slate-600 dark:text-slate-300">
                            <MapPin class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400" />
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Location & Timezone</p>
                                <p>{{ props.profile.location || 'Available for Remote Roles' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                        <a
                            v-if="props.profile.github_url"
                            :href="props.profile.github_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-lg border border-slate-200 p-2.5 text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="GitHub"
                        >
                            <GithubIcon class="h-5 w-5" />
                        </a>
                        <a
                            v-if="props.profile.linkedin_url"
                            :href="props.profile.linkedin_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-lg border border-slate-200 p-2.5 text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-800"
                            aria-label="LinkedIn"
                        >
                            <LinkedinIcon class="h-5 w-5" />
                        </a>
                        <a
                            v-if="props.profile.resume_path"
                            href="/resume"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-800"
                        >
                            <Download class="h-4 w-4" />
                            Resume
                        </a>
                    </div>
                </div>

                <section
                    v-if="faqItems.length"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
                    aria-labelledby="contact-faq-heading"
                >
                    <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <h2 id="contact-faq-heading" class="text-lg font-bold text-slate-900 dark:text-white">
                            FAQ
                        </h2>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                            Quick answers before you send a note.
                        </p>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <div v-for="(faq, index) in faqItems" :key="faq.question">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                                :aria-expanded="openFaq === index"
                                @click="toggleFaq(index)"
                            >
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ faq.question }}</span>
                                <ChevronDown
                                    class="h-4 w-4 shrink-0 text-slate-500 transition-transform"
                                    :class="openFaq === index ? 'rotate-180' : ''"
                                />
                            </button>
                            <div
                                v-show="openFaq === index"
                                class="px-5 pb-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400"
                            >
                                {{ faq.answer }}
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                <div v-if="isSubmitted" class="space-y-4 py-12 text-center">
                    <CheckCircle2 class="mx-auto h-14 w-14 text-emerald-500" />
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Message Dispatched!</h3>
                    <p class="mx-auto max-w-md text-slate-600 dark:text-slate-400">
                        Thank you for reaching out. Your message has been saved and I will respond as soon as possible.
                    </p>
                    <button
                        type="button"
                        class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        @click="isSubmitted = false"
                    >
                        Send Another Note
                    </button>
                </div>

                <form v-else class="space-y-6" @submit.prevent="submitContact">
                    <input v-model="form._hp" type="text" name="_hp" class="hidden" tabindex="-1" autocomplete="off" />

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Your Name</label>
                            <input
                                v-model="form.name"
                                required
                                type="text"
                                placeholder="Jane Doe"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
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
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
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
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
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
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        />
                        <p v-if="form.errors.message" class="text-xs text-rose-500">{{ form.errors.message }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 font-bold text-white shadow-md transition-colors hover:bg-indigo-700 disabled:opacity-50 sm:w-auto"
                    >
                        <Send class="h-4 w-4" />
                        <span>{{ form.processing ? 'Dispatching...' : 'Send Message' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>