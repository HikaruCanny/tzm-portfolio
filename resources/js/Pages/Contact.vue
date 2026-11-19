<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Reveal from '@/components/Reveal.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import {
    CheckCircle2,
    Download,
    HelpCircle,
    Mail,
    MapPin,
    Phone,
    MessageSquare,
    Minus,
    Plus,
    Send,
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Profile {
    name?: string;
    email?: string;
    phone?: string | null;
    github_url?: string;
    linkedin_url?: string;
    location?: string;
    has_resume?: boolean;
    resume_url?: string | null;
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
        email: 'thantzinmoedev07@gmail.com',
        location: 'Myanmar · Open to global remote roles',
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

const inputClass =
    'w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-white/15 dark:bg-white/10 dark:text-white dark:placeholder:text-slate-400 dark:focus:border-primary-400 dark:focus:bg-white/[0.14] dark:focus:ring-primary-400/20';

const faqTone = (index: number): string => `skill-tone-${index % 3}`;

const faqHeadingTone = (index: number): string =>
    [
        'text-purple-700 dark:text-purple-300',
        'text-violet-700 dark:text-violet-300',
        'text-cyan-700 dark:text-cyan-300',
    ][index % 3] ?? 'text-purple-700 dark:text-purple-300';
</script>

<template>
    <div class="section-bg-c">
        <Head title="Contact – TZM" />

        <div id="faq" class="mx-auto max-w-6xl scroll-mt-24 space-y-14 px-4 py-16 sm:px-6 lg:px-8">
            <Reveal>
                <header class="max-w-2xl space-y-4">
                    <p class="text-special text-xs font-semibold uppercase tracking-[0.24em]">
                        Contact
                    </p>
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl dark:text-white">
                        Send a note
                    </h1>
                    <p class="text-lg leading-relaxed text-slate-600 dark:text-slate-300">
                        Interested in a role, a product build, or architecture advice? Write directly — messages land in the admin inbox.
                    </p>
                </header>
            </Reveal>

            <Reveal :delay="80">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)] lg:gap-10">
                    <aside class="space-y-5">
                        <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">
                                Reach me
                            </h2>
                            <ul class="mt-5 space-y-4">
                                <li>
                                    <a
                                        :href="`mailto:${props.profile.email || 'contact@tzm.dev'}`"
                                        class="group flex items-start gap-3"
                                    >
                                        <span class="skill-tone-0 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                            <Mail class="h-4 w-4" />
                                        </span>
                                        <span>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-purple-700 dark:text-purple-300">Email</span>
                                            <span class="text-sm font-medium text-slate-700 transition group-hover:text-primary-600 dark:text-slate-200 dark:group-hover:text-primary-300">
                                                {{ props.profile.email || 'contact@tzm.dev' }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li v-if="props.profile.phone">
                                    <a
                                        :href="`tel:${(props.profile.phone || '').replace(/\s/g, '')}`"
                                        class="group flex items-start gap-3"
                                    >
                                        <span class="skill-tone-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                            <Phone class="h-4 w-4" />
                                        </span>
                                        <span>
                                            <span class="block text-xs font-semibold uppercase tracking-wide text-violet-700 dark:text-violet-300">Phone</span>
                                            <span class="text-sm font-medium text-slate-700 transition group-hover:text-primary-600 dark:text-slate-200 dark:group-hover:text-primary-300">
                                                {{ props.profile.phone }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="skill-tone-3 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                        <MapPin class="h-4 w-4" />
                                    </span>
                                    <span>
                                        <span class="block text-xs font-semibold uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Location</span>
                                        <span class="text-sm text-slate-700 dark:text-slate-200">
                                            {{ props.profile.location || 'Available for Remote Roles' }}
                                        </span>
                                    </span>
                                </li>
                            </ul>

                            <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5 dark:border-white/10">
                                <a
                                    v-if="props.profile.github_url"
                                    :href="props.profile.github_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:border-primary-300 hover:bg-primary-50 dark:border-slate-700 dark:text-slate-300 dark:hover:border-white/20 dark:hover:bg-white/5"
                                    aria-label="GitHub"
                                >
                                    <GithubIcon class="h-5 w-5" />
                                </a>
                                <a
                                    v-if="props.profile.linkedin_url"
                                    :href="props.profile.linkedin_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:border-primary-300 hover:bg-primary-50 dark:border-slate-700 dark:text-slate-300 dark:hover:border-white/20 dark:hover:bg-white/5"
                                    aria-label="LinkedIn"
                                >
                                    <LinkedinIcon class="h-5 w-5" />
                                </a>
                                <a
                                    v-if="props.profile.has_resume"
                                    :href="props.profile.resume_url ?? '/resume'"
                                    download
                                    class="skill-tone-0 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold"
                                >
                                    <Download class="h-3.5 w-3.5" />
                                    Resume
                                </a>
                            </div>
                        </div>

                        <div class="skill-tone-1 rounded-2xl border border-primary-100/80 px-5 py-4 dark:border-white/10">
                            <p class="flex items-center gap-2 text-sm font-medium text-violet-800 dark:text-violet-200">
                                <MessageSquare class="h-4 w-4 shrink-0" />
                                I usually reply from the email listed above.
                            </p>
                        </div>
                    </aside>

                    <div class="rounded-2xl border border-primary-100 bg-white p-6 shadow-sm sm:p-8 dark:border-white/10 dark:bg-white/5">
                        <div v-if="isSubmitted" class="flex flex-col items-start justify-center gap-4 py-8">
                            <CheckCircle2 class="h-10 w-10 text-emerald-500 dark:text-emerald-400" />
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Message received</h2>
                            <p class="max-w-md text-slate-600 dark:text-slate-300">
                                Thank you — I'll respond as soon as I can.
                            </p>
                            <button
                                type="button"
                                class="text-sm font-semibold text-primary-600 hover:underline dark:text-primary-300"
                                @click="isSubmitted = false"
                            >
                                Send another message
                            </button>
                        </div>

                        <form v-else class="space-y-5" @submit.prevent="submitContact">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Your message</h2>
                                <p class="mt-1 text-sm text-slate-500">Fields marked <span class="text-required">*</span> are required.</p>
                            </div>

                            <input v-model="form._hp" type="text" name="_hp" class="hidden" tabindex="-1" autocomplete="off" />

                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="space-y-1.5 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Name <span class="text-required">*</span></span>
                                    <input v-model="form.name" required type="text" placeholder="Jane Doe" :class="inputClass" />
                                    <span v-if="form.errors.name" class="text-xs text-rose-500">{{ form.errors.name }}</span>
                                </label>
                                <label class="space-y-1.5 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Email <span class="text-required">*</span></span>
                                    <input v-model="form.email" required type="email" placeholder="jane@example.com" :class="inputClass" />
                                    <span v-if="form.errors.email" class="text-xs text-rose-500">{{ form.errors.email }}</span>
                                </label>
                            </div>

                            <label class="block space-y-1.5 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Subject</span>
                                <input
                                    v-model="form.subject"
                                    type="text"
                                    placeholder="Project, role, or question"
                                    :class="inputClass"
                                />
                                <span v-if="form.errors.subject" class="text-xs text-rose-500">{{ form.errors.subject }}</span>
                            </label>

                            <label class="block space-y-1.5 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Message <span class="text-required">*</span></span>
                                <textarea
                                    v-model="form.message"
                                    required
                                    rows="5"
                                    placeholder="Team context, timeline, or technical goals…"
                                    :class="inputClass"
                                />
                                <span v-if="form.errors.message" class="text-xs text-rose-500">{{ form.errors.message }}</span>
                            </label>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn-send inline-flex items-center gap-2 rounded-full px-6 py-2.5 text-sm font-semibold transition"
                            >
                                <Send class="h-4 w-4" />
                                <span>{{ form.processing ? 'Sending…' : 'Send message' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </Reveal>

            <Reveal v-if="faqItems.length" :delay="140">
                <section class="space-y-8" aria-labelledby="contact-faq-heading">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="space-y-3">
                            <p class="skill-tone-1 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">
                                <HelpCircle class="h-3.5 w-3.5" />
                                FAQ
                            </p>
                            <div>
                                <h2 id="contact-faq-heading" class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    Common questions
                                </h2>
                                <p class="mt-1 max-w-xl text-sm text-slate-500 dark:text-slate-400">
                                    Tap a card to expand. One section stays open at a time.
                                </p>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-slate-400">
                            {{ faqItems.length }} topics
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <article
                            v-for="(faq, index) in faqItems"
                            :key="faq.question"
                            class="flex flex-col rounded-2xl border bg-white p-1 shadow-sm transition duration-300 dark:bg-white/5"
                            :class="[
                                openFaq === index
                                    ? 'border-primary-300/80 shadow-md ring-2 ring-primary-500/10 dark:border-primary-400/40 dark:ring-primary-400/15'
                                    : 'border-primary-100 hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md dark:border-white/10 dark:hover:border-white/20',
                                index === faqItems.length - 1 && faqItems.length % 2 === 1 ? 'sm:col-span-2 lg:max-w-2xl' : '',
                            ]"
                        >
                            <button
                                type="button"
                                class="flex w-full items-start gap-3 rounded-[0.9rem] p-4 text-left"
                                :aria-expanded="openFaq === index"
                                @click="toggleFaq(index)"
                            >
                                <span
                                    :class="[
                                        'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition',
                                        openFaq === index ? faqTone(index) : 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                                    ]"
                                >
                                    <Minus v-if="openFaq === index" class="h-4 w-4" />
                                    <Plus v-else class="h-4 w-4" />
                                </span>
                                <span class="min-w-0 flex-1 pt-0.5">
                                    <span
                                        class="block text-sm font-semibold leading-snug transition-colors sm:text-base"
                                        :class="openFaq === index ? faqHeadingTone(index) : 'text-slate-900 dark:text-white'"
                                    >
                                        {{ faq.question }}
                                    </span>
                                </span>
                            </button>

                            <div
                                v-show="openFaq === index"
                                class="mx-4 mb-4 rounded-xl px-4 py-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300"
                                :class="faqTone(index)"
                            >
                                {{ faq.answer }}
                            </div>
                        </article>
                    </div>
                </section>
            </Reveal>
        </div>
    </div>
</template>