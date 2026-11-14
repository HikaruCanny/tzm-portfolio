<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Reveal from '@/components/Reveal.vue';
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import {
    ArrowRight,
    Award,
    Briefcase,
    Calendar,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Database,
    FileText,
    FolderKanban,
    Quote,
    GraduationCap,
    LayoutDashboard,
    Mail,
    MapPin,
    Send,
    Server,
    ShieldCheck,
    ShoppingCart,
    Wrench,
    Zap,
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Technology {
    id: number;
    name: string;
}

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    cover_image?: string | null;
    demo_url?: string;
    repo_url?: string;
    is_featured: boolean;
    technologies: Technology[];
}

interface TestimonialItem {
    id: number;
    author_name: string;
    author_role?: string | null;
    quote: string;
}

interface ExperienceItem {
    id: number;
    type: 'work' | 'education' | 'certification';
    organization: string;
    role: string;
    start_date: string;
    end_date?: string | null;
    description?: string;
}

interface Skill {
    id: number;
    name: string;
}

interface SkillCategory {
    id: number;
    name: string;
    skills: Skill[];
}

interface Service {
    id: number;
    title: string;
    description: string;
    icon?: string;
}

interface Profile {
    name?: string;
    title?: string;
    tagline?: string;
    bio?: string;
    email?: string;
    location?: string;
    photo_path?: string | null;
    resume_path?: string | null;
    github_url?: string;
    linkedin_url?: string;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
    experiences?: ExperienceItem[];
    categories?: SkillCategory[];
    services?: Service[];
    featuredProjects?: Project[];
    testimonials?: TestimonialItem[];
}>(), {
    profile: () => ({
        name: 'TZM',
        title: 'Full-Stack Developer (Laravel + Vue.js)',
        tagline: 'Architecting reliable backend APIs and reactive single-page interfaces.',
    }),
    experiences: () => [],
    categories: () => [],
    services: () => [],
    featuredProjects: () => [],
    testimonials: () => [],
});

const contactSent = ref(false);
const slide = ref(0);
const paused = ref(false);
let slideTimer: ReturnType<typeof setInterval> | undefined;

const headingTone = 'bg-gradient-to-r from-indigo-700 via-violet-700 to-fuchsia-700 bg-clip-text text-transparent dark:from-indigo-300 dark:via-violet-300 dark:to-fuchsia-300';

const showSlide = (index: number) => {
    const total = props.testimonials.length;

    if (total === 0) {
        return;
    }

    slide.value = (index + total) % total;
};

const stopSlides = () => {
    if (slideTimer) {
        clearInterval(slideTimer);
    }
};

const startSlides = () => {
    stopSlides();

    if (props.testimonials.length < 2) {
        return;
    }

    slideTimer = setInterval(() => {
        if (!paused.value) {
            showSlide(slide.value + 1);
        }
    }, 5000);
};

onMounted(startSlides);
onBeforeUnmount(stopSlides);

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    _hp: '',
});

const initials = (props.profile.name || 'TZM').slice(0, 1).toUpperCase();

const authorInitial = (name: string): string => name.slice(0, 1).toUpperCase();

const period = (item: ExperienceItem): string => {
    const start = item.start_date?.substring(0, 7) ?? '';
    const end = item.end_date ? item.end_date.substring(0, 7) : 'Present';

    return `${start} – ${end}`;
};

const serviceIcon = (iconName?: string) => {
    switch (iconName) {
        case 'LayoutDashboard': return LayoutDashboard;
        case 'Server': return Server;
        case 'ShoppingCart': return ShoppingCart;
        case 'Zap': return Zap;
        case 'ShieldCheck': return ShieldCheck;
        case 'Database': return Database;
        default: return Wrench;
    }
};

const submitContact = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            contactSent.value = true;
            form.reset();
        },
    });
};
</script>

<template>
    <div>
        <Head :title="`${props.profile.name || 'TZM'} – ${props.profile.title || 'Full-Stack Developer'}`" />

        <section id="home" class="scroll-mt-24">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:py-24 lg:px-8">
                <div id="about" class="scroll-mt-28 lg:col-span-7">
                    <p :class="headingTone" class="text-xs font-semibold uppercase tracking-[0.28em]">
                        {{ props.profile.location || 'Portfolio' }}
                    </p>
                    <h1 :class="headingTone" class="mt-4 text-5xl font-extrabold tracking-tight sm:text-7xl">
                        {{ props.profile.name || 'TZM' }}
                    </h1>
                    <p :class="headingTone" class="mt-4 text-xl font-medium">
                        {{ props.profile.title || 'Full-Stack Developer' }}
                    </p>
                    <p v-if="props.profile.tagline" class="mt-5 max-w-xl text-lg leading-relaxed text-slate-500">
                        {{ props.profile.tagline }}
                    </p>
                    <p class="mt-6 max-w-xl whitespace-pre-line text-base leading-7 text-slate-600 dark:text-slate-300">
                        {{ props.profile.bio }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link href="/projects" class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-600 dark:bg-white dark:text-slate-950">
                            <span>View projects</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <a href="/#contact" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-indigo-500 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-200">
                            Contact
                        </a>
                    </div>
                    <div v-if="props.profile.email || props.profile.resume_path" class="mt-6 grid max-w-xl gap-3 sm:grid-cols-2">
                        <a
                            v-if="props.profile.email"
                            :href="`mailto:${props.profile.email}`"
                            class="flex items-center gap-3 rounded-2xl border border-indigo-100 bg-white px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-300 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500 text-white">
                                <Mail class="h-4 w-4" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Email</span>
                                <span class="block truncate text-sm font-semibold text-slate-900 dark:text-white">{{ props.profile.email }}</span>
                            </span>
                        </a>
                        <a
                            v-if="props.profile.resume_path"
                            href="/resume"
                            class="flex items-center gap-3 rounded-2xl border border-indigo-100 bg-white px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-300 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500 text-white">
                                <FileText class="h-4 w-4" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Resume</span>
                                <span class="block truncate text-sm font-semibold text-slate-900 dark:text-white">Download PDF</span>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5">
                    <div class="relative mx-auto max-w-sm">
                        <div class="absolute -left-4 top-6 h-full w-full rounded-[2rem] bg-gradient-to-br from-indigo-100 via-violet-100 to-fuchsia-100 dark:from-indigo-950 dark:via-violet-950 dark:to-fuchsia-950"></div>
                        <img
                            v-if="props.profile.photo_path"
                            :src="`/storage/${props.profile.photo_path}`"
                            :alt="props.profile.name || 'Profile photo'"
                            class="relative aspect-[4/5] w-full rounded-[2rem] object-cover shadow-xl"
                        />
                        <div v-else class="relative flex aspect-[4/5] w-full items-center justify-center rounded-[2rem] bg-indigo-600 text-7xl font-extrabold text-white shadow-xl">
                            {{ initials }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="experience" class="scroll-mt-24 py-20">
            <Reveal class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600 dark:text-indigo-400">Experience</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Work, study, and certificates</h2>

                <div v-if="props.experiences.length" class="mt-10 space-y-6">
                    <article
                        v-for="item in props.experiences"
                        :key="item.id"
                        class="grid gap-3 border-t border-slate-200 py-6 dark:border-slate-800 md:grid-cols-[11rem_1fr]"
                    >
                        <p class="flex items-center gap-2 text-sm text-slate-500">
                            <Calendar class="h-4 w-4" />
                            <span>{{ period(item) }}</span>
                        </p>
                        <div class="space-y-2">
                            <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">
                                <Briefcase v-if="item.type === 'work'" class="h-3.5 w-3.5" />
                                <GraduationCap v-else-if="item.type === 'education'" class="h-3.5 w-3.5" />
                                <Award v-else class="h-3.5 w-3.5" />
                                <span>{{ item.role }}</span>
                            </p>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ item.organization }}</h3>
                            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ item.description }}</p>
                        </div>
                    </article>
                </div>
                <p v-else class="mt-8 text-slate-500">No timeline milestones recorded yet.</p>
            </Reveal>
        </section>

        <section id="skills" class="scroll-mt-24 mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <Reveal>
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600 dark:text-indigo-400">Skills</p>
            <h2 class="mt-3 max-w-xl text-3xl font-extrabold text-slate-900 dark:text-white">Tools grouped by the work they support</h2>

            <div v-if="props.categories.length" class="mt-10">
                <div
                    v-for="category in props.categories"
                    :key="category.id"
                    class="grid gap-4 border-t border-slate-200 py-6 dark:border-slate-800 md:grid-cols-[12rem_1fr] md:items-start"
                >
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ category.name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="skill in category.skills"
                            :key="skill.id"
                            class="rounded-full bg-white px-3 py-1.5 text-sm text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-800"
                        >
                            {{ skill.name }}
                        </span>
                    </div>
                </div>
            </div>
            <p v-else class="mt-8 text-slate-500">No skills recorded yet.</p>
            </Reveal>
        </section>

        <section id="services" class="scroll-mt-24 bg-white py-20 dark:bg-slate-900">
            <Reveal class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600 dark:text-indigo-400">Services</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">What I can build</h2>
                <div v-if="props.services.length" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="service in props.services"
                        :key="service.id"
                        class="rounded-2xl border border-slate-200 p-6 dark:border-slate-800"
                    >
                        <component :is="serviceIcon(service.icon)" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                        <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">{{ service.title }}</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ service.description }}</p>
                    </article>
                </div>
                <p v-else class="mt-8 text-slate-500">No services currently listed.</p>
            </Reveal>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
            <Reveal>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600 dark:text-indigo-400">Featured</p>
                        <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Featured projects</h2>
                    </div>
                    <Link href="/projects" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                        <span>All projects</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </Reveal>
            <div v-if="props.featuredProjects.length" class="mt-8 grid gap-8 md:grid-cols-3">
                <Reveal v-for="(project, index) in props.featuredProjects" :key="project.id" :delay="index * 120">
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                        <Link
                            v-if="project.cover_image"
                            :href="`/projects/${project.slug}`"
                            class="block aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800"
                        >
                            <img :src="`/storage/${project.cover_image}`" :alt="`${project.title} cover`" class="h-full w-full object-cover" />
                        </Link>
                        <div class="space-y-3 p-5">
                            <div v-if="!project.cover_image" class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                                <FolderKanban class="h-5 w-5" />
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                <Link :href="`/projects/${project.slug}`" class="hover:text-indigo-600">{{ project.title }}</Link>
                            </h3>
                            <p class="line-clamp-3 text-sm text-slate-600 dark:text-slate-400">{{ project.summary }}</p>
                            <Link :href="`/projects/${project.slug}`" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                <span>Case study</span>
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </article>
                </Reveal>
            </div>
            <p v-else class="mt-8 text-slate-500">No featured projects currently listed.</p>
        </section>

        <section id="testimonials" class="scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <Reveal>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Testimonials</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">What people say</h2>
                </Reveal>
                <div
                    v-if="props.testimonials.length"
                    class="relative mt-10 overflow-hidden rounded-[2rem] border border-indigo-100 bg-white text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                >
                    <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-fuchsia-200 blur-3xl dark:bg-fuchsia-500/30"></div>
                    <div class="pointer-events-none absolute -bottom-24 -left-10 h-56 w-56 rounded-full bg-indigo-200 blur-3xl dark:bg-indigo-500/30"></div>
                    <div class="flex transition-transform duration-500 ease-out" :style="{ transform: `translateX(-${slide * 100}%)` }">
                        <blockquote v-for="item in props.testimonials" :key="item.id" class="relative w-full shrink-0 px-6 py-12 sm:px-12 lg:px-16">
                            <Quote class="h-10 w-10 text-indigo-600 dark:text-indigo-300" />
                            <p class="mt-6 max-w-3xl text-2xl font-medium leading-snug text-slate-900 sm:text-3xl dark:text-white">“{{ item.quote }}”</p>
                            <footer class="mt-8 flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white dark:bg-white/10">{{ authorInitial(item.author_name) }}</span>
                                <span>
                                    <span class="block font-semibold">{{ item.author_name }}</span>
                                    <span v-if="item.author_role" class="block text-sm text-slate-500 dark:text-slate-300">{{ item.author_role }}</span>
                                </span>
                            </footer>
                        </blockquote>
                    </div>
                    <div v-if="props.testimonials.length > 1" class="relative flex items-center justify-between px-6 pb-8 sm:px-12 lg:px-16">
                        <div class="flex gap-2">
                            <button
                                v-for="(item, index) in props.testimonials"
                                :key="item.id"
                                type="button"
                                class="h-2 rounded-full transition-all"
                                :class="index === slide ? 'w-8 bg-indigo-600 dark:bg-white' : 'w-2 bg-indigo-200 dark:bg-white/30'"
                                :aria-label="`Show testimonial ${index + 1}`"
                                @click="showSlide(index)"
                            />
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="rounded-full border border-indigo-200 p-2 text-indigo-700 hover:bg-indigo-50 dark:border-white/20 dark:text-white dark:hover:bg-white/10" aria-label="Previous testimonial" @click="showSlide(slide - 1)">
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                            <button type="button" class="rounded-full border border-indigo-200 p-2 text-indigo-700 hover:bg-indigo-50 dark:border-white/20 dark:text-white dark:hover:bg-white/10" aria-label="Next testimonial" @click="showSlide(slide + 1)">
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
                <p v-else class="mt-8 text-slate-500">No testimonials yet.</p>
            </div>
        </section>

        <section id="contact" class="scroll-mt-24 px-4 pb-20 sm:px-6 lg:px-8">
            <Reveal>
            <div class="mx-auto grid max-w-6xl gap-10 rounded-[2rem] border border-indigo-100 bg-white px-6 py-12 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white sm:px-10 lg:grid-cols-[0.8fr_1.2fr] lg:px-14">
                <div class="space-y-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-700 dark:text-indigo-300">Contact</p>
                    <h2 class="text-3xl font-extrabold">Send a note</h2>
                    <p class="text-slate-600 dark:text-slate-300">
                        Write about a role, a product, or a question. The message goes straight to the admin inbox.
                    </p>
                    <div class="space-y-3 text-sm text-slate-700 dark:text-slate-200">
                        <a v-if="props.profile.email" :href="`mailto:${props.profile.email}`" class="flex items-center gap-2 hover:text-indigo-700 dark:hover:text-white">
                            <Mail class="h-4 w-4 text-indigo-600 dark:text-indigo-300" />
                            <span>{{ props.profile.email }}</span>
                        </a>
                        <p v-if="props.profile.location" class="flex items-center gap-2">
                            <MapPin class="h-4 w-4 text-indigo-600 dark:text-indigo-300" />
                            <span>{{ props.profile.location }}</span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a v-if="props.profile.github_url" :href="props.profile.github_url" target="_blank" class="rounded-full border border-indigo-200 p-2 text-slate-700 hover:bg-indigo-50 dark:border-white/15 dark:text-white dark:hover:bg-white/10" aria-label="GitHub">
                            <GithubIcon class="h-5 w-5" />
                        </a>
                        <a v-if="props.profile.linkedin_url" :href="props.profile.linkedin_url" target="_blank" class="rounded-full border border-indigo-200 p-2 text-slate-700 hover:bg-indigo-50 dark:border-white/15 dark:text-white dark:hover:bg-white/10" aria-label="LinkedIn">
                            <LinkedinIcon class="h-5 w-5" />
                        </a>
                    </div>
                </div>

                <div v-if="contactSent" class="flex flex-col items-start justify-center gap-3">
                    <CheckCircle2 class="h-10 w-10 text-emerald-500 dark:text-emerald-400" />
                    <h3 class="text-2xl font-bold">Message received</h3>
                    <p class="text-slate-600 dark:text-slate-300">I will reply from the address on this page.</p>
                    <button type="button" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-white" @click="contactSent = false">
                        Send another message
                    </button>
                </div>

                <form v-else class="space-y-4" @submit.prevent="submitContact">
                    <input v-model="form._hp" type="text" name="_hp" tabindex="-1" autocomplete="off" class="hidden" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1 text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Name</span>
                            <input v-model="form.name" required type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-white/5 dark:text-white" />
                            <span v-if="form.errors.name" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.name }}</span>
                        </label>
                        <label class="space-y-1 text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Email</span>
                            <input v-model="form.email" required type="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-white/5 dark:text-white" />
                            <span v-if="form.errors.email" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.email }}</span>
                        </label>
                    </div>
                    <label class="block space-y-1 text-sm">
                        <span class="text-slate-600 dark:text-slate-300">Subject</span>
                        <input v-model="form.subject" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-white/5 dark:text-white" />
                    </label>
                    <label class="block space-y-1 text-sm">
                        <span class="text-slate-600 dark:text-slate-300">Message</span>
                        <textarea v-model="form.message" required rows="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-white/5 dark:text-white"></textarea>
                        <span v-if="form.errors.message" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.message }}</span>
                    </label>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-60 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-100">
                        <Send class="h-4 w-4" />
                        <span>{{ form.processing ? 'Sending…' : 'Send message' }}</span>
                    </button>
                </form>
            </div>
            </Reveal>
        </section>
    </div>
</template>
