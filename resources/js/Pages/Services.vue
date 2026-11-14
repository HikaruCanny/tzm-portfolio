<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { 
    LayoutDashboard, 
    Server, 
    ShoppingCart, 
    Zap, 
    ShieldCheck, 
    Database, 
    ArrowRight,
    Wrench
} from '@lucide/vue';

defineOptions({ layout: PublicLayout });

interface Service {
    id: number;
    title: string;
    description: string;
    icon?: string;
}

const props = withDefaults(defineProps<{
    services?: Service[];
}>(), {
    services: () => [],
});

const getServiceIcon = (iconName?: string) => {
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
</script>

<template>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        <Head title="What I Can Build – TZM" />

        <div class="space-y-4 max-w-2xl">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                What I Can Build
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                End-to-end full-stack development services tailored for startups, businesses, and engineering teams.
            </p>
        </div>

        <div v-if="props.services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                v-for="service in props.services"
                :key="service.id"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:border-indigo-500/50 hover:shadow-md transition-all space-y-4"
            >
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <component :is="getServiceIcon(service.icon)" class="w-6 h-6" />
                </div>

                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    {{ service.title }}
                </h3>

                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                    {{ service.description }}
                </p>
            </div>
        </div>

        <div v-else class="text-center py-12 text-slate-500 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <p>No services currently listed.</p>
        </div>

        <div class="pt-8 text-center">
            <Link
                href="/contact"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-700 transition-all"
            >
                <span>Discuss Your Project</span>
                <ArrowRight class="w-4 h-4" />
            </Link>
        </div>
    </div>
</template>
