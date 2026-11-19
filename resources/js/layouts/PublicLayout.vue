<script setup lang="ts">
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import { computed, ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    Sun, 
    Moon, 
    Menu as MenuIcon, 
    X as XIcon, 
    ArrowUp,
    Code2,
    Briefcase,
    GraduationCap,
    Send,
    Terminal,
    Layers
} from '@lucide/vue';

const page = usePage();
const isDark = ref(false);
const mobileMenuOpen = ref(false);

const portfolio = computed(() => page.props.portfolio as { has_resume?: boolean; resume_url?: string | null } | undefined);

const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'About', href: '/about' },
    { name: 'Experience', href: '/experience' },
    { name: 'Skills', href: '/skills' },
    { name: 'Services', href: '/services' },
    { name: 'Projects', href: '/projects' },
    { name: 'Blog', href: '/blog' },
    { name: 'Contact', href: '/contact' },
];

onMounted(() => {
    // Check initial dark mode preference
    if (
        localStorage.getItem('theme') === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <div class="min-h-screen scroll-smooth bg-transparent text-slate-800 transition-colors duration-200 dark:text-slate-100 flex flex-col font-sans">
        <!-- Sticky Navbar -->
        <header class="sticky top-0 z-50 border-b border-stone-200/80 bg-[color:var(--section-a)] transition-colors dark:border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2 group">
                    <span class="w-9 h-9 rounded-lg bg-primary-600 text-white flex items-center justify-center font-bold text-lg shadow-md group-hover:scale-105 transition-transform">
                        T
                    </span>
                    <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-primary-700 to-primary-500 dark:from-primary-300 dark:to-primary-400 bg-clip-text text-transparent">
                        TZM.dev
                    </span>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:text-primary-700 dark:text-slate-300 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 transition-all"
                    >
                        {{ link.name }}
                    </a>
                    <a
                        v-if="portfolio?.has_resume"
                        :href="portfolio.resume_url ?? '/resume'"
                        download
                        class="px-3 py-2 rounded-md text-sm font-medium text-primary-700 hover:text-primary-800 dark:text-primary-300 dark:hover:text-primary-200 hover:bg-primary-50 dark:hover:bg-primary-950/40 transition-all"
                    >
                        Resume
                    </a>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <button
                        @click="toggleTheme"
                        type="button"
                        aria-label="Toggle theme"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
                        <Moon v-else class="w-5 h-5 text-slate-600" />
                    </button>

                    <a
                        href="/login"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center justify-center text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        Admin
                    </a>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                        aria-label="Toggle menu"
                    >
                        <XIcon v-if="mobileMenuOpen" class="w-6 h-6" />
                        <MenuIcon v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown -->
            <div v-show="mobileMenuOpen" class="md:hidden border-b border-stone-200 bg-[color:var(--section-a)] px-4 pt-2 pb-4 space-y-1 dark:border-white/10">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-primary-50 dark:hover:bg-primary-950/50 hover:text-primary-700 dark:hover:text-primary-300"
                >
                    {{ link.name }}
                </a>
                <a
                    v-if="portfolio?.has_resume"
                    :href="portfolio.resume_url ?? '/resume'"
                    download
                    class="block px-3 py-2 rounded-md text-base font-medium text-primary-700 dark:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-950/50"
                    @click="mobileMenuOpen = false"
                >
                    Resume
                </a>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                    <a
                        href="/login"
                        target="_blank"
                        rel="noopener noreferrer"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2 rounded-md text-sm font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white"
                    >
                        Admin Portal
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-stone-200/80 bg-[color:var(--section-a)] py-10 transition-colors dark:border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded bg-primary-600 text-white font-bold flex items-center justify-center text-sm">
                        T
                    </span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                        © {{ new Date().getFullYear() }} TZM Portfolio. Built with Laravel, Vue 3, Inertia & Tailwind.
                    </span>
                </div>

                <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400">
                    <a
                        href="https://github.com"
                        target="_blank"
                        rel="noreferrer"
                        class="hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                        aria-label="GitHub"
                    >
                        <GithubIcon class="w-5 h-5" />
                    </a>
                    <a
                        href="https://linkedin.com"
                        target="_blank"
                        rel="noreferrer"
                        class="hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                        aria-label="LinkedIn"
                    >
                        <LinkedinIcon class="w-5 h-5" />
                    </a>
                    <button
                        @click="scrollToTop"
                        type="button"
                        class="ml-4 p-2 rounded-full border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors"
                        title="Back to top"
                    >
                        <ArrowUp class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </footer>
    </div>
</template>
