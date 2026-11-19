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
    Phone,
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
    cover_url: string;
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

interface LatestPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    published_at?: string | null;
    reading_time: number;
    tags?: Array<{ id: number; name: string; slug: string }>;
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
    phone?: string | null;
    location?: string;
    photo_path?: string | null;
    has_resume?: boolean;
    resume_url?: string | null;
    github_url?: string;
    linkedin_url?: string;
}

const props = withDefaults(defineProps<{
    profile?: Profile;
    experiences?: ExperienceItem[];
    categories?: SkillCategory[];
    services?: Service[];
    featuredProjects?: Project[];
    latestPosts?: LatestPost[];
    testimonials?: TestimonialItem[];
}>(), {
    profile: () => ({
        name: 'Thant Zin Moe',
        title: 'Full-Stack Developer (Laravel + Vue.js)',
        tagline: 'Laravel APIs and Vue interfaces that stay maintainable after launch.',
    }),
    experiences: () => [],
    categories: () => [],
    services: () => [],
    featuredProjects: () => [],
    latestPosts: () => [],
    testimonials: () => [],
});

const formatPostDate = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const contactSent = ref(false);
const slide = ref(0);
const paused = ref(false);
let slideTimer: ReturnType<typeof setInterval> | undefined;

const skillTone = (index: number): string => `skill-tone-${index % 6}`;
const purpleIconTone = (index: number): string => `skill-tone-${index % 3}`;
const titleCyan = 'text-cyan-700 transition-colors hover:text-cyan-800 dark:text-cyan-300 dark:hover:text-cyan-200';
const actionLightPurple = 'skill-tone-0';

const brand = {
    kicker: 'text-special',
    link: 'text-primary-700 dark:text-primary-300',
    linkHover: 'hover:text-primary-800 dark:hover:text-primary-200',
    icon: 'text-primary-600 dark:text-primary-400',
    cardHover: 'hover:border-primary-300 dark:hover:border-primary-500/40',
};

const sectionAccent = {
    experience: {
        ...brand,
        badge: brand.kicker,
        dotBorder: 'border-primary-600 dark:border-primary-400',
        dotFill: 'bg-primary-600 dark:bg-primary-400',
        line: 'from-primary-600 to-primary-400 dark:from-primary-400 dark:to-primary-300',
    },
    skills: {
        ...brand,
        pill: 'ring-primary-200/80 dark:ring-primary-800/60',
        divider: 'border-primary-200/70 dark:border-primary-900/50',
    },
    services: {
        ...brand,
    },
    projects: {
        ...brand,
        iconTile: 'chip-light',
        titleHover: 'transition-colors hover:text-primary-700 dark:hover:text-primary-300',
    },
    blog: {
        ...brand,
        titleHover: 'transition-colors hover:text-primary-700 dark:hover:text-primary-300',
    },
    testimonials: {
        ...brand,
        icon: 'text-primary-600 dark:text-primary-300',
        shell: 'border-primary-100 dark:border-primary-900/40',
        blurA: 'bg-primary-200 dark:bg-primary-500/25',
        blurB: 'bg-primary-100 dark:bg-primary-400/20',
        avatar: 'bg-primary-600 dark:bg-primary-500/90',
        dotActive: 'bg-primary-600 dark:bg-white',
        dotIdle: 'bg-primary-200 dark:bg-white/30',
        navBtn: 'border-primary-200 text-primary-800 hover:bg-primary-50 dark:border-white/20 dark:text-white dark:hover:bg-white/10',
    },
    contact: {
        ...brand,
        btn: 'btn-send',
        focus: 'focus:border-primary-500 focus:ring-primary-500/20 dark:focus:border-primary-400 dark:focus:ring-primary-400/20',
    },
} as const;

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

        <section id="home" class="section-bg-a scroll-mt-24">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:py-24 lg:px-8">
                <div id="about" class="scroll-mt-28 lg:col-span-7">
                    <p class="skill-tone-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.28em]">
                        {{ props.profile.location || 'Portfolio' }}
                    </p>
                    <h1 class="mt-4 text-5xl font-extrabold tracking-tight text-violet-700 sm:text-7xl dark:text-violet-300">
                        {{ props.profile.name || 'TZM' }}
                    </h1>
                    <p class="skill-tone-0 mt-4 inline-flex rounded-lg px-3 py-1.5 text-xl font-medium">
                        {{ props.profile.title || 'Full-Stack Developer' }}
                    </p>
                    <p v-if="props.profile.tagline" class="mt-5 max-w-xl text-lg leading-relaxed text-slate-500">
                        {{ props.profile.tagline }}
                    </p>
                    <p class="mt-6 max-w-xl whitespace-pre-line text-base leading-7 text-slate-600 dark:text-slate-300">
                        {{ props.profile.bio }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link href="/projects" class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400">
                            <span>View projects</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <a href="/#contact" class="inline-flex items-center rounded-full border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-primary-500 hover:text-primary-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-primary-400 dark:hover:text-primary-300">
                            Contact
                        </a>
                    </div>
                    <div v-if="props.profile.email || props.profile.phone || props.profile.has_resume" class="mt-6 grid max-w-xl gap-3 sm:grid-cols-2">
                        <a
                            v-if="props.profile.email"
                            :href="`mailto:${props.profile.email}`"
                            class="flex items-center gap-3 rounded-2xl border border-primary-100 bg-white px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-700 to-primary-500 text-white">
                                <Mail class="h-4 w-4" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Email</span>
                                <span class="block truncate text-sm font-semibold text-slate-900 dark:text-white">{{ props.profile.email }}</span>
                            </span>
                        </a>
                        <a
                            v-if="props.profile.phone"
                            :href="`tel:${props.profile.phone.replace(/\s/g, '')}`"
                            class="flex items-center gap-3 rounded-2xl border border-primary-100 bg-white px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-700 to-primary-500 text-white">
                                <Phone class="h-4 w-4" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Phone</span>
                                <span class="block truncate text-sm font-semibold text-slate-900 dark:text-white">{{ props.profile.phone }}</span>
                            </span>
                        </a>
                        <a
                            v-if="props.profile.has_resume"
                            :href="props.profile.resume_url ?? '/resume'"
                            download
                            class="flex items-center gap-3 rounded-2xl border border-primary-100 bg-white px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 dark:border-slate-800 dark:bg-slate-900"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-700 to-primary-500 text-white">
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
                        <div class="absolute -left-4 top-6 h-full w-full rounded-[2rem] bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-950 dark:to-primary-900"></div>
                        <img
                            v-if="props.profile.photo_path"
                            :src="`/storage/${props.profile.photo_path}`"
                            :alt="props.profile.name || 'Profile photo'"
                            class="relative aspect-[4/5] w-full rounded-[2rem] object-cover shadow-xl"
                        />
                        <div v-else class="relative flex aspect-[4/5] w-full items-center justify-center rounded-[2rem] bg-primary-600 text-7xl font-extrabold text-white shadow-xl">
                            {{ initials }}
                        </div>
                    </div>
                </div>
            </div>
            <svg
                class="section-curve"
                viewBox="0 0 1440 72"
                preserveAspectRatio="none"
                aria-hidden="true"
            >
                <defs>
                    <linearGradient id="about-experience-curve" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--section-a)" />
                        <stop offset="45%" stop-color="var(--section-a)" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="var(--section-b)" />
                    </linearGradient>
                </defs>
                <path fill="url(#about-experience-curve)" d="M0 72C240 24 480 0 720 0s480 24 720 72H0z" />
            </svg>
        </section>

        <section id="experience" class="section-bg-b section-to-a scroll-mt-24 py-20">
            <Reveal class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.experience.kicker]">Experience</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Work, study, and certificates</h2>

                <div v-if="props.experiences.length" class="relative mt-10">
                    <div
                        aria-hidden="true"
                        :class="['absolute bottom-0 left-6 top-0 w-0.5 -translate-x-1/2 rounded-full bg-gradient-to-b', sectionAccent.experience.line]"
                    />
                    <div class="space-y-8">
                    <article
                        v-for="(item, experienceIndex) in props.experiences"
                        :key="item.id"
                        class="relative flex gap-6 sm:gap-8"
                    >
                        <div class="relative z-10 flex w-12 shrink-0 justify-center pt-6">
                            <div
                                aria-hidden="true"
                                :class="['flex h-5 w-5 items-center justify-center rounded-full border-2 bg-white shadow-sm dark:bg-slate-950', sectionAccent.experience.dotBorder]"
                            >
                                <span :class="['h-2 w-2 rounded-full', sectionAccent.experience.dotFill]" />
                            </div>
                        </div>
                        <div :class="['min-w-0 flex-1 rounded-2xl border border-primary-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-white/5 sm:p-6', sectionAccent.experience.cardHover]">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span :class="['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold uppercase tracking-wide', skillTone(experienceIndex)]">
                                    <Briefcase v-if="item.type === 'work'" class="h-3.5 w-3.5" />
                                    <GraduationCap v-else-if="item.type === 'education'" class="h-3.5 w-3.5" />
                                    <Award v-else class="h-3.5 w-3.5" />
                                    <span>{{ item.role }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                    <Calendar class="h-3.5 w-3.5" />
                                    {{ period(item) }}
                                </span>
                            </div>
                            <h3 class="mt-3 text-xl font-bold text-slate-900 dark:text-white">{{ item.organization }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ item.description }}</p>
                        </div>
                    </article>
                    </div>
                </div>
                <p v-else class="mt-8 text-slate-500">No timeline milestones recorded yet.</p>
            </Reveal>
        </section>

        <section id="skills" class="section-bg-a section-to-c scroll-mt-24 py-20">
            <Reveal class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.skills.kicker]">Skills</p>
            <h2 class="mt-3 max-w-xl text-3xl font-extrabold text-slate-900 dark:text-white">Tools grouped by the work they support</h2>

            <div v-if="props.categories.length" class="mt-10">
                <div
                    v-for="(category, categoryIndex) in props.categories"
                    :key="category.id"
                    :class="['grid gap-4 border-t py-6 md:grid-cols-[12rem_1fr] md:items-start', sectionAccent.skills.divider]"
                >
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ category.name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="(skill, skillIndex) in category.skills"
                            :key="skill.id"
                            :class="['rounded-full px-3 py-1.5 text-sm font-medium', skillTone(categoryIndex * 3 + skillIndex)]"
                        >
                            {{ skill.name }}
                        </span>
                    </div>
                </div>
            </div>
            <p v-else class="mt-8 text-slate-500">No skills recorded yet.</p>
            </Reveal>
        </section>

        <section id="services" class="section-bg-c section-to-b scroll-mt-24 py-20">
            <Reveal class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.services.kicker]">Services</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">What I can build</h2>
                <div v-if="props.services.length" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(service, serviceIndex) in props.services"
                        :key="service.id"
                        :class="['rounded-2xl border border-primary-100 bg-white p-6 transition hover:-translate-y-0.5 hover:shadow-sm dark:border-white/10 dark:bg-white/5', sectionAccent.services.cardHover]"
                    >
                        <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(serviceIndex)]">
                            <component :is="serviceIcon(service.icon)" class="h-5 w-5" />
                        </div>
                        <h3 :class="['mt-4 text-lg font-bold', titleCyan]">{{ service.title }}</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ service.description }}</p>
                    </article>
                </div>
                <p v-else class="mt-8 text-slate-500">No services currently listed.</p>
            </Reveal>
        </section>

        <section id="projects" class="section-bg-b section-to-a scroll-mt-24 py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <Reveal>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.projects.kicker]">Featured</p>
                        <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Featured projects</h2>
                    </div>
                    <Link href="/projects" :class="['inline-flex items-center gap-1 text-sm font-semibold hover:underline', sectionAccent.projects.link]">
                        <span>All projects</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </Reveal>
            <div v-if="props.featuredProjects.length" class="mt-8 grid gap-8 md:grid-cols-3">
                <Reveal v-for="(project, index) in props.featuredProjects" :key="project.id" :delay="index * 120">
                    <article :class="['overflow-hidden rounded-2xl border border-primary-100 bg-white transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-white/5', sectionAccent.projects.cardHover]">
                        <Link
                            :href="`/projects/${project.slug}`"
                            class="block aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800"
                        >
                            <img :src="project.cover_url" :alt="`${project.title} cover`" class="h-full w-full object-cover" />
                        </Link>
                        <div class="space-y-3 p-5">
                            <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(index)]">
                                <FolderKanban class="h-5 w-5" />
                            </div>
                            <h3 class="text-lg font-bold">
                                <Link
                                    :href="`/projects/${project.slug}`"
                                    :class="titleCyan"
                                >{{ project.title }}</Link>
                            </h3>
                            <p class="line-clamp-3 text-sm text-slate-600 dark:text-slate-400">{{ project.summary }}</p>
                            <Link :href="`/projects/${project.slug}`" :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-sm font-semibold', actionLightPurple]">
                                <span>Case study</span>
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </article>
                </Reveal>
            </div>
            <p v-else class="mt-8 text-slate-500">No featured projects currently listed.</p>
            </div>
        </section>

        <section id="blog" class="section-bg-a section-to-b scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <Reveal>
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.blog.kicker]">Blog</p>
                            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">Latest writing</h2>
                        </div>
                        <Link href="/blog" :class="['inline-flex items-center gap-1 text-sm font-semibold hover:underline', sectionAccent.blog.link]">
                            <span>All articles</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
                <div v-if="props.latestPosts.length" class="mt-8 grid gap-6 md:grid-cols-3">
                    <Reveal v-for="(post, index) in props.latestPosts" :key="post.id" :delay="index * 120">
                        <article :class="['space-y-3 rounded-2xl border border-primary-100 bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md dark:border-white/10 dark:bg-white/5', sectionAccent.blog.cardHover]">
                            <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', purpleIconTone(index)]">
                                <FileText class="h-5 w-5" />
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span v-if="post.published_at" class="inline-flex items-center gap-1">
                                    <Calendar class="h-3.5 w-3.5" />
                                    {{ formatPostDate(post.published_at) }}
                                </span>
                                <span>{{ post.reading_time }} min read</span>
                            </div>
                            <h3 class="text-lg font-bold">
                                <Link :href="`/blog/${post.slug}`" :class="titleCyan">{{ post.title }}</Link>
                            </h3>
                            <p class="line-clamp-3 text-sm text-slate-600 dark:text-slate-400">{{ post.excerpt }}</p>
                            <Link :href="`/blog/${post.slug}`" :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-sm font-semibold', actionLightPurple]">
                                <span>Read</span>
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </article>
                    </Reveal>
                </div>
                <p v-else class="mt-8 text-slate-500">No articles published yet.</p>
            </div>
        </section>

        <section id="testimonials" class="testimonials-section section-bg-b section-to-c scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <Reveal>
                    <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.testimonials.kicker]">Testimonials</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-slate-900 dark:text-white">What people say</h2>
                </Reveal>
                <div
                    v-if="props.testimonials.length"
                    :class="['relative mt-10 overflow-hidden rounded-[2rem] border bg-white text-slate-900 shadow-sm dark:bg-slate-950 dark:text-white', sectionAccent.testimonials.shell]"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                >
                    <div :class="['pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full blur-3xl', sectionAccent.testimonials.blurB]"></div>
                    <div :class="['pointer-events-none absolute -bottom-24 -left-10 h-56 w-56 rounded-full blur-3xl', sectionAccent.testimonials.blurA]"></div>
                    <div class="flex transition-transform duration-500 ease-out" :style="{ transform: `translateX(-${slide * 100}%)` }">
                        <blockquote v-for="item in props.testimonials" :key="item.id" class="relative w-full shrink-0 px-6 py-12 sm:px-12 lg:px-16">
                            <Quote :class="['h-10 w-10', sectionAccent.testimonials.icon]" />
                            <p class="mt-6 max-w-3xl text-2xl font-medium leading-snug text-slate-900 sm:text-3xl dark:text-white">“{{ item.quote }}”</p>
                            <footer class="mt-8 flex items-center gap-3">
                                <span :class="['flex h-11 w-11 items-center justify-center rounded-full text-sm font-bold text-white', sectionAccent.testimonials.avatar]">{{ authorInitial(item.author_name) }}</span>
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
                                :class="index === slide ? ['w-8', sectionAccent.testimonials.dotActive] : ['w-2', sectionAccent.testimonials.dotIdle]"
                                :aria-label="`Show testimonial ${index + 1}`"
                                @click="showSlide(index)"
                            />
                        </div>
                        <div class="flex gap-2">
                            <button type="button" :class="['rounded-full border p-2', sectionAccent.testimonials.navBtn]" aria-label="Previous testimonial" @click="showSlide(slide - 1)">
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                            <button type="button" :class="['rounded-full border p-2', sectionAccent.testimonials.navBtn]" aria-label="Next testimonial" @click="showSlide(slide + 1)">
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
                <p v-else class="mt-8 text-slate-500">No testimonials yet.</p>
            </div>
        </section>

        <section id="contact" class="section-bg-c scroll-mt-24 px-4 py-20 sm:px-6 lg:px-8">
            <Reveal>
            <div class="mx-auto grid max-w-6xl gap-10 rounded-2xl border border-slate-200 bg-white px-6 py-12 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-white sm:px-10 lg:grid-cols-[0.8fr_1.2fr] lg:px-14">
                <div class="space-y-6">
                    <p :class="['text-xs font-semibold uppercase tracking-[0.22em]', sectionAccent.contact.kicker]">Contact</p>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Send a note</h2>
                    <p class="text-slate-600 dark:text-slate-400">
                        Write about a role, a product, or a question. The message goes straight to the admin inbox.
                    </p>
                    <div class="space-y-3 text-sm text-slate-700 dark:text-slate-300">
                        <a v-if="props.profile.email" :href="`mailto:${props.profile.email}`" :class="['flex items-center gap-2', sectionAccent.contact.link, sectionAccent.contact.linkHover]">
                            <Mail :class="['h-4 w-4', sectionAccent.contact.icon]" />
                            <span>{{ props.profile.email }}</span>
                        </a>
                        <a
                            v-if="props.profile.phone"
                            :href="`tel:${props.profile.phone.replace(/\s/g, '')}`"
                            :class="['flex items-center gap-2', sectionAccent.contact.link, sectionAccent.contact.linkHover]"
                        >
                            <Phone :class="['h-4 w-4', sectionAccent.contact.icon]" />
                            <span>{{ props.profile.phone }}</span>
                        </a>
                        <p v-if="props.profile.location" class="flex items-center gap-2">
                            <MapPin :class="['h-4 w-4', sectionAccent.contact.icon]" />
                            <span>{{ props.profile.location }}</span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a v-if="props.profile.github_url" :href="props.profile.github_url" target="_blank" class="rounded-full border border-slate-200 p-2 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" aria-label="GitHub">
                            <GithubIcon class="h-5 w-5" />
                        </a>
                        <a v-if="props.profile.linkedin_url" :href="props.profile.linkedin_url" target="_blank" class="rounded-full border border-slate-200 p-2 text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" aria-label="LinkedIn">
                            <LinkedinIcon class="h-5 w-5" />
                        </a>
                    </div>
                </div>

                <div v-if="contactSent" class="flex flex-col items-start justify-center gap-3">
                    <CheckCircle2 class="h-10 w-10 text-emerald-500 dark:text-emerald-400" />
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Message received</h3>
                    <p class="text-slate-600 dark:text-slate-400">I will reply from the address on this page.</p>
                    <button type="button" :class="['text-sm font-semibold', sectionAccent.contact.link, sectionAccent.contact.linkHover]" @click="contactSent = false">
                        Send another message
                    </button>
                </div>

                <form v-else class="space-y-4" @submit.prevent="submitContact">
                    <input v-model="form._hp" type="text" name="_hp" tabindex="-1" autocomplete="off" class="hidden" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1 text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-300">Name <span class="text-required">*</span></span>
                            <input v-model="form.name" required type="text" :class="['w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 outline-none transition focus:ring-2 dark:border-white/15 dark:bg-white/10 dark:text-white dark:focus:bg-white/[0.14]', sectionAccent.contact.focus]" />
                            <span v-if="form.errors.name" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.name }}</span>
                        </label>
                        <label class="space-y-1 text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-300">Email <span class="text-required">*</span></span>
                            <input v-model="form.email" required type="email" :class="['w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 outline-none transition focus:ring-2 dark:border-white/15 dark:bg-white/10 dark:text-white dark:focus:bg-white/[0.14]', sectionAccent.contact.focus]" />
                            <span v-if="form.errors.email" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.email }}</span>
                        </label>
                    </div>
                    <label class="block space-y-1 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Subject</span>
                        <input v-model="form.subject" type="text" :class="['w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 outline-none transition focus:ring-2 dark:border-white/15 dark:bg-white/10 dark:text-white dark:focus:bg-white/[0.14]', sectionAccent.contact.focus]" />
                    </label>
                    <label class="block space-y-1 text-sm">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Message <span class="text-required">*</span></span>
                        <textarea v-model="form.message" required rows="5" :class="['w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-900 outline-none transition focus:ring-2 dark:border-white/15 dark:bg-white/10 dark:text-white dark:focus:bg-white/[0.14]', sectionAccent.contact.focus]"></textarea>
                        <span v-if="form.errors.message" class="text-xs text-rose-600 dark:text-rose-300">{{ form.errors.message }}</span>
                    </label>
                    <button type="submit" :disabled="form.processing" :class="['inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition', sectionAccent.contact.btn]">
                        <Send class="h-4 w-4" />
                        <span>{{ form.processing ? 'Sending…' : 'Send message' }}</span>
                    </button>
                </form>
            </div>
            </Reveal>
        </section>
    </div>
</template>
