<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { CircleUser, ExternalLink, FolderKanban, Briefcase, Hash, LayoutDashboard, LogOut, Mail, Menu as MenuIcon, Moon, Newspaper, Quote, ScrollText, Sparkles, Sun, Tags, Ticket, User, Users, Wrench, X as XIcon } from '@lucide/vue';
import FlashMessage from '@/components/FlashMessage.vue';

const page = usePage();
const sidebarOpen = ref(false);
const isDark = ref(false);

const isSuperAdmin = computed(() => page.props.auth?.user?.role === 'superadmin');

const navItems = computed(() => {
    const items = [
        { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
        { name: 'Projects', href: '/admin/projects', icon: FolderKanban },
        { name: 'Experience', href: '/admin/experiences', icon: Briefcase },
        { name: 'Skills', href: '/admin/skills', icon: Sparkles },
        { name: 'Technologies', href: '/admin/technologies', icon: Tags },
        { name: 'Posts', href: '/admin/posts', icon: Newspaper },
        { name: 'Tags', href: '/admin/tags', icon: Hash },
        { name: 'Services', href: '/admin/services', icon: Wrench },
        { name: 'Testimonials', href: '/admin/testimonials', icon: Quote },
        { name: 'Messages', href: '/admin/messages', icon: Mail },
        { name: 'My account', href: '/admin/account', icon: CircleUser },
    ];

    if (isSuperAdmin.value) {
        items.push(
            { name: 'Site Profile', href: '/admin/profile', icon: User },
            { name: 'Invitations', href: '/admin/invitations', icon: Ticket },
            { name: 'Admins', href: '/admin/users', icon: Users },
            { name: 'Activity', href: '/admin/activity', icon: ScrollText },
        );
    }

    return items;
});

onMounted(() => {
    if (
        localStorage.getItem('theme') === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
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

const isActive = (href: string) => {
    if (href === '/admin') {
        return page.url === '/admin';
    }
    return page.url.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100 flex transition-colors">
        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex flex-col w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shrink-0">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200 dark:border-slate-800">
                <Link href="/admin" class="flex items-center gap-2 font-bold text-lg text-indigo-600 dark:text-indigo-400">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        A
                    </span>
                    <span class="text-slate-900 dark:text-white">TZM Admin</span>
                </Link>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors',
                        isActive(item.href)
                            ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'
                    ]"
                >
                    <component :is="item.icon" class="w-4 h-4 shrink-0" />
                    <span>{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Bottom actions -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
                <Link
                    href="/"
                    target="_blank"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                    <ExternalLink class="w-3.5 h-3.5" />
                    <span>View Public Portfolio</span>
                </Link>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors"
                >
                    <LogOut class="w-3.5 h-3.5" />
                    <span>Sign Out</span>
                </Link>
            </div>
        </aside>

        <!-- Main Column -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    type="button"
                    class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                >
                    <MenuIcon class="w-5 h-5" />
                </button>

                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 hidden sm:inline">
                        Signed in as <span class="text-indigo-600 dark:text-indigo-400">{{ $page.props.auth?.user?.name || 'Admin' }}</span>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        @click="toggleTheme"
                        type="button"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        <Sun v-if="isDark" class="w-4 h-4 text-amber-400" />
                        <Moon v-else class="w-4 h-4 text-slate-600" />
                    </button>
                </div>
            </header>

            <!-- Mobile Drawer -->
            <div v-show="sidebarOpen" class="md:hidden fixed inset-0 z-50 bg-black/50" @click="sidebarOpen = false">
                <div class="flex h-full w-64 flex-col bg-white dark:bg-slate-900" @click.stop>
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4 dark:border-slate-800">
                        <span class="font-bold text-slate-900 dark:text-white">Navigation</span>
                        <button type="button" @click="sidebarOpen = false">
                            <XIcon class="w-5 h-5 text-slate-500" />
                        </button>
                    </div>
                    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-4">
                        <Link
                            v-for="item in navItems"
                            :key="item.name"
                            :href="item.href"
                            @click="sidebarOpen = false"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            <component :is="item.icon" class="w-4 h-4" />
                            <span>{{ item.name }}</span>
                        </Link>
                    </nav>
                    <div class="space-y-2 border-t border-slate-200 p-4 dark:border-slate-800">
                        <Link
                            href="/"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                        >
                            <ExternalLink class="h-3.5 w-3.5" />
                            <span>View Public Portfolio</span>
                        </Link>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                        >
                            <LogOut class="h-3.5 w-3.5" />
                            <span>Sign Out</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
                <FlashMessage />
                <slot />
            </main>
        </div>
    </div>
</template>

