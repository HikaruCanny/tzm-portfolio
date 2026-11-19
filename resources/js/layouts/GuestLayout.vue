<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-transparent pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden border border-indigo-100/80 bg-gradient-to-br from-white via-indigo-50/70 to-cyan-50 px-6 py-4 shadow-[0_18px_50px_-28px_rgba(79,70,229,0.45)] sm:max-w-md sm:rounded-2xl dark:border-indigo-500/20 dark:from-slate-950 dark:via-indigo-950/50 dark:to-slate-900"
        >
            <slot />
        </div>
    </div>
</template>
