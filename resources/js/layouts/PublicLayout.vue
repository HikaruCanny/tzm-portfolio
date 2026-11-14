<script setup lang="ts">
import GithubIcon from '@/components/icons/GithubIcon.vue';
import LinkedinIcon from '@/components/icons/LinkedinIcon.vue';
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
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

const isDark = ref(false);
const mobileMenuOpen = ref(false);

const navLinks = [
    { name: 'Home', href: '/#home' },
    { name: 'About', href: '/#about' },
    { name: 'Experience', href: '/#experience' },
    { name: 'Skills', href: '/#skills' },
    { name: 'Services', href: '/#services' },
    { name: 'Projects', href: '/projects' },
    { name: 'Blog', href: '/blog' },
    { name: 'Contact', href: '/#contact' },
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
    <div class="min-h-screen scroll-smooth bg-slate-50 text-slate-800 transition-colors duration-200 dark:bg-slate-950 dark:text-slate-100 flex flex-col font-sans">
        <!-- Sticky Navbar -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2 group">
                    <span class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md group-hover:scale-105 transition-transform">
                        T
                    </span>
                    <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent">
                        TZM.dev
                    </span>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition-all"
                    >
                        {{ link.name }}
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

                    <Link
                        href="/login"
                        class="hidden sm:inline-flex items-center justify-center text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        Admin
                    </Link>

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
            <div v-show="mobileMenuOpen" class="md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 pt-2 pb-4 space-y-1">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-400"
                >
                    {{ link.name }}
                </a>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                    <Link
                        href="/login"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2 rounded-md text-sm font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white"
                    >
                        Admin Portal
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-10 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded bg-indigo-600 text-white font-bold flex items-center justify-center text-sm">
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
                        class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                        aria-label="GitHub"
                    >
                        <GithubIcon class="w-5 h-5" />
                    </a>
                    <a
                        href="https://linkedin.com"
                        target="_blank"
                        rel="noreferrer"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
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
