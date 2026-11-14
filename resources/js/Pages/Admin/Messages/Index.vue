<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Mail, Check, Trash2, Clock } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

defineProps<{
    messages: {
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

const markAsRead = (id: number) => {
    router.patch(`/admin/messages/${id}/read`);
};

const deleteMessage = (id: number) => {
    if (confirm('Delete this visitor message?')) {
        router.delete(`/admin/messages/${id}`);
    }
};
</script>

<template>
    <div class="space-y-6 max-w-5xl mx-auto">
        <Head title="Visitor Messages – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Visitor Inquiries & Messages
            </h1>
            <p class="text-sm text-slate-500">
                Submissions from the public contact form.
            </p>
        </div>

        <div v-if="messages.data.length > 0" class="space-y-4">
            <div
                v-for="msg in messages.data"
                :key="msg.id"
                class="bg-white dark:bg-slate-900 border rounded-2xl p-6 shadow-sm space-y-4 transition-colors"
                :class="msg.read_at ? 'border-slate-200 dark:border-slate-800' : 'border-indigo-400 dark:border-indigo-600 ring-1 ring-indigo-400/20'"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 dark:text-white">{{ msg.name }}</span>
                        <span class="text-xs text-slate-400">&lt;{{ msg.email }}&gt;</span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <Clock class="w-3.5 h-3.5" />
                        <span>{{ new Date(msg.created_at).toLocaleString() }}</span>
                        <span 
                            class="px-2 py-0.5 rounded-full font-semibold ml-2"
                            :class="msg.read_at ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'"
                        >
                            {{ msg.read_at ? 'Read' : 'New' }}
                        </span>
                    </div>
                </div>

                <div v-if="msg.subject" class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Subject: {{ msg.subject }}
                </div>

                <div class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/60 p-4 rounded-xl whitespace-pre-wrap font-sans">
                    {{ msg.message }}
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        v-if="!msg.read_at"
                        @click="markAsRead(msg.id)"
                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1"
                    >
                        <Check class="w-3.5 h-3.5" />
                        <span>Mark as Read</span>
                    </button>
                    <button
                        @click="deleteMessage(msg.id)"
                        class="text-xs font-semibold text-red-500 hover:underline inline-flex items-center gap-1"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <Mail class="w-10 h-10 text-slate-400 mx-auto mb-2" />
            <p class="text-slate-600 dark:text-slate-400 font-medium">Your contact inbox is currently clear.</p>
        </div>

        <Pagination :paginator="messages" />
    </div>
</template>

