<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Plus, Trash2 } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

interface Testimonial {
    id: number;
    author_name: string;
    author_role?: string | null;
    quote: string;
    sort_order: number;
    is_published: boolean;
}

defineProps<{
    testimonials: {
        data: Testimonial[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const showAdd = ref(false);

const form = useForm({
    author_name: '',
    author_role: '',
    quote: '',
    sort_order: 0,
    is_published: true,
});

const submit = () => {
    form.post('/admin/testimonials', {
        onSuccess: () => {
            showAdd.value = false;
            form.reset();
            form.is_published = true;
        },
    });
};

const remove = (id: number) => {
    if (confirm('Remove this testimonial?')) {
        router.delete(`/admin/testimonials/${id}`);
    }
};
</script>

<template>
    <div class="mx-auto max-w-5xl space-y-6">
        <Head title="Testimonials – TZM Admin" />

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Testimonials</h1>
                <p class="text-sm text-slate-500">Quotes that appear on the home page. Use real notes you have permission to publish.</p>
            </div>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" @click="showAdd = !showAdd">
                <Plus class="h-4 w-4" />
                <span>Add testimonial</span>
            </button>
        </div>

        <form v-if="showAdd" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                    Name
                    <input v-model="form.author_name" required type="text" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950" />
                </label>
                <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                    Role
                    <input v-model="form.author_role" type="text" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950" />
                </label>
            </div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">
                Quote
                <textarea v-model="form.quote" required rows="4" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"></textarea>
            </label>
            <p v-if="form.errors.quote" class="text-xs text-red-600">{{ form.errors.quote }}</p>
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input v-model="form.is_published" type="checkbox" class="rounded border-slate-300 text-indigo-600" />
                Show on the home page
            </label>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg border px-4 py-2 text-sm" @click="showAdd = false">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" :disabled="form.processing">Save</button>
            </div>
        </form>

        <div v-if="testimonials.data.length" class="space-y-4">
            <article v-for="item in testimonials.data" :key="item.id" class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-slate-700 dark:text-slate-200">“{{ item.quote }}”</p>
                <div class="mt-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ item.author_name }}</p>
                        <p class="text-sm text-slate-500">{{ item.author_role }}</p>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-red-600" title="Remove" @click="remove(item.id)">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </article>
        </div>
        <p v-else class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900">No testimonials yet.</p>
        <Pagination :paginator="testimonials" />
    </div>
</template>
