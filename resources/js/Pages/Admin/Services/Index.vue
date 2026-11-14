<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Database, LayoutDashboard, Plus, Server, ShieldCheck, ShoppingCart, Trash2, Wrench, Zap } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    services: any[];
}>();

const showAdd = ref(false);

const icons = [
    { name: 'LayoutDashboard', component: LayoutDashboard },
    { name: 'Server', component: Server },
    { name: 'ShoppingCart', component: ShoppingCart },
    { name: 'Zap', component: Zap },
    { name: 'ShieldCheck', component: ShieldCheck },
    { name: 'Database', component: Database },
    { name: 'Wrench', component: Wrench },
];

const iconFor = (name?: string | null) => icons.find((icon) => icon.name === name)?.component ?? Wrench;

const form = useForm({
    title: '',
    description: '',
    icon: 'Wrench',
    sort_order: 0,
});

const submit = () => {
    form.post('/admin/services', {
        onSuccess: () => {
            showAdd.value = false;
            form.reset();
        }
    });
};

const deleteService = (id: number) => {
    if (confirm('Delete this service capability?')) {
        router.delete(`/admin/services/${id}`);
    }
};
</script>

<template>
    <div class="space-y-6 max-w-5xl mx-auto">
        <Head title="Services & Capabilities – TZM Admin" />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Services / "What I Can Build"
                </h1>
                <p class="text-sm text-slate-500">
                    Manage the technical offerings and services showcased to clients and recruiters.
                </p>
            </div>

            <button
                @click="showAdd = !showAdd"
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow hover:bg-indigo-700 transition-colors"
            >
                <Plus class="w-4 h-4" />
                <span>Add Capability</span>
            </button>
        </div>

        <div v-if="showAdd" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white">New Service Card</h2>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Title</label>
                    <input v-model="form.title" required type="text" placeholder="E.g. Full-Stack SaaS Development" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Description</label>
                    <textarea v-model="form.description" required rows="3" placeholder="Describe the service in 2-3 sentences..." class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"></textarea>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Icon</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button
                            v-for="icon in icons"
                            :key="icon.name"
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border transition"
                            :class="form.icon === icon.name ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 text-slate-600 hover:border-indigo-300 dark:border-slate-700 dark:text-slate-300'"
                            :aria-label="icon.name"
                            :aria-pressed="form.icon === icon.name"
                            @click="form.icon = icon.name"
                        >
                            <component :is="icon.component" class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.icon" class="mt-1 text-xs text-red-600">{{ form.errors.icon }}</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button @click="showAdd = false" type="button" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold text-sm">Save Service</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                v-for="svc in services"
                :key="svc.id"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-3 flex flex-col justify-between"
            >
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                            <component :is="iconFor(svc.icon)" class="h-4 w-4" />
                        </span>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ svc.title }}</h3>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ svc.description }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button @click="deleteService(svc.id)" class="text-xs text-red-500 hover:underline">
                        Remove Card
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

