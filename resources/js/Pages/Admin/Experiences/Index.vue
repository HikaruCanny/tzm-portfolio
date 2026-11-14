<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Plus, Trash2, Briefcase, GraduationCap, Calendar } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    experiences: {
        data: any[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const showAddModal = ref(false);

const form = useForm({
    type: 'work',
    organization: '',
    role: '',
    start_date: '',
    end_date: '',
    description: '',
    sort_order: 0,
});

const submit = () => {
    form.post('/admin/experiences', {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
        }
    });
};

const deleteItem = (id: number) => {
    if (confirm('Delete this milestone entry?')) {
        router.delete(`/admin/experiences/${id}`);
    }
};
</script>

<template>
    <div class="space-y-6 max-w-5xl mx-auto">
        <Head title="Experience Timeline – TZM Admin" />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Experience & Education Timeline
                </h1>
                <p class="text-sm text-slate-500">
                    Manage milestones displayed on your public experience page.
                </p>
            </div>

            <button
                @click="showAddModal = !showAddModal"
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow hover:bg-indigo-700 transition-colors"
            >
                <Plus class="w-4 h-4" />
                <span>Add Milestone</span>
            </button>
        </div>

        <!-- Add Form Modal / Dropdown -->
        <div v-if="showAddModal" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="font-bold text-slate-900 dark:text-white">New Timeline Entry</h2>
            <form @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Type</label>
                    <select v-model="form.type" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm">
                        <option value="work">Work Experience</option>
                        <option value="education">Education</option>
                        <option value="certification">Certification</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Organization / Institution</label>
                    <input v-model="form.organization" required type="text" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Role / Degree Title</label>
                    <input v-model="form.role" required type="text" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Start Date</label>
                    <input v-model="form.start_date" required type="date" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">End Date (leave blank if current)</label>
                    <input v-model="form.end_date" type="date" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Sort Order</label>
                    <input v-model="form.sort_order" type="number" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm" />
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Description</label>
                    <textarea v-model="form.description" rows="2" class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"></textarea>
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button @click="showAddModal = false" type="button" class="px-4 py-2 rounded-lg border text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold text-sm">Save Milestone</button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div
                v-for="item in experiences.data"
                :key="item.id"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex items-start justify-between gap-4"
            >
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-0.5 rounded font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 capitalize">
                            {{ item.type }}
                        </span>
                        <span class="text-xs text-slate-400">{{ item.start_date }} – {{ item.end_date || 'Present' }}</span>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">
                        {{ item.role }} <span class="text-slate-400 font-normal">at</span> {{ item.organization }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ item.description }}</p>
                </div>

                <button
                    @click="deleteItem(item.id)"
                    class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                    title="Delete milestone"
                >
                    <Trash2 class="w-4 h-4" />
                </button>
            </div>
        </div>
        <Pagination :paginator="experiences" />
    </div>
</template>

