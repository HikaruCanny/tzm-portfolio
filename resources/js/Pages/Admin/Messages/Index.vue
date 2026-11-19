<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { Mail, Check, Trash2, Clock, Send, Flag, AlertTriangle } from '@lucide/vue';
import { ref } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
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
    filter: 'all' | 'unread' | 'spam' | 'handled';
}>();

const filters = [
    { key: 'all', label: 'All' },
    { key: 'unread', label: 'Unread' },
    { key: 'handled', label: 'Handled' },
    { key: 'spam', label: 'Spam' },
] as const;

const setFilter = (key: typeof props.filter) => {
    router.get('/admin/messages', { filter: key }, { preserveState: true, preserveScroll: true });
};

const markAsRead = (id: number) => {
    router.patch(`/admin/messages/${id}/read`);
};

const markAsHandled = (id: number) => {
    router.patch(`/admin/messages/${id}/handled`);
};

const markAllRead = () => {
    if (confirm('Mark every unread message as read?')) {
        router.patch('/admin/messages/read-all');
    }
};

const deleteMessage = (id: number) => {
    if (confirm('Delete this visitor message?')) {
        router.delete(`/admin/messages/${id}`);
    }
};

const openReplyFor = ref<number | null>(null);
const replyForm = useForm({ body: '' });

const toggleReply = (id: number) => {
    if (openReplyFor.value === id) {
        openReplyFor.value = null;
        return;
    }

    openReplyFor.value = id;
    replyForm.clearErrors();
    replyForm.reset();
};

const sendReply = (id: number) => {
    replyForm.post(`/admin/messages/${id}/reply`, {
        preserveScroll: true,
        onSuccess: () => {
            openReplyFor.value = null;
            replyForm.reset();
        },
    });
};
</script>

<template>
    <div class="space-y-6 max-w-5xl mx-auto">
        <Head title="Visitor Messages – TZM Admin" />

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Visitor Inquiries & Messages
                </h1>
                <p class="text-sm text-slate-500">
                    Submissions from the public contact form. Spam is hidden from notifications.
                </p>
            </div>
            <button
                type="button"
                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
                @click="markAllRead"
            >
                Mark all unread as read
            </button>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                v-for="item in filters"
                :key="item.key"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors"
                :class="
                    filter === item.key
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-300'
                "
                @click="setFilter(item.key)"
            >
                {{ item.label }}
            </button>
        </div>

        <div v-if="messages.data.length > 0" class="space-y-4">
            <div
                v-for="msg in messages.data"
                :key="msg.id"
                class="bg-white dark:bg-slate-900 border rounded-2xl p-6 shadow-sm space-y-4 transition-colors"
                :class="[
                    msg.is_spam
                        ? 'border-amber-300 dark:border-amber-700'
                        : msg.read_at
                          ? 'border-slate-200 dark:border-slate-800'
                          : 'border-indigo-400 dark:border-indigo-600 ring-1 ring-indigo-400/20',
                ]"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-slate-900 dark:text-white">{{ msg.name }}</span>
                        <span class="text-xs text-slate-400">&lt;{{ msg.email }}&gt;</span>
                        <span
                            v-if="msg.is_spam"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200"
                        >
                            <AlertTriangle class="w-3 h-3" />
                            Spam
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-400 flex-wrap">
                        <Clock class="w-3.5 h-3.5" />
                        <span>{{ new Date(msg.created_at).toLocaleString() }}</span>
                        <span
                            v-if="!msg.is_spam"
                            class="px-2 py-0.5 rounded-full font-semibold"
                            :class="
                                msg.handled_at
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                    : msg.read_at
                                      ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                      : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'
                            "
                        >
                            {{ msg.handled_at ? 'Handled' : msg.read_at ? 'Read' : 'New' }}
                        </span>
                    </div>
                </div>

                <div v-if="msg.subject" class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                    Subject: {{ msg.subject }}
                </div>

                <div
                    class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/60 p-4 rounded-xl whitespace-pre-wrap font-sans"
                >
                    {{ msg.message }}
                </div>

                <div v-if="msg.replies?.length" class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Your replies</p>
                    <div
                        v-for="reply in msg.replies"
                        :key="reply.id"
                        class="text-sm rounded-xl bg-indigo-50 dark:bg-indigo-950/40 p-3 text-slate-700 dark:text-slate-200"
                    >
                        <p class="text-xs text-slate-500 mb-1">
                            {{ reply.user?.name ?? 'Admin' }} ·
                            {{ new Date(reply.sent_at).toLocaleString() }}
                        </p>
                        <p class="whitespace-pre-wrap">{{ reply.body }}</p>
                    </div>
                </div>

                <div v-if="openReplyFor === msg.id && !msg.is_spam" class="space-y-2">
                    <textarea
                        v-model="replyForm.body"
                        rows="4"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm p-3"
                        placeholder="Write your reply to the visitor…"
                    />
                    <p v-if="replyForm.errors.body" class="text-xs text-red-500">
                        {{ replyForm.errors.body }}
                    </p>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500"
                            :disabled="replyForm.processing"
                            @click="sendReply(msg.id)"
                        >
                            <Send class="w-3.5 h-3.5" />
                            Send reply
                        </button>
                        <button
                            type="button"
                            class="text-xs font-semibold text-slate-500 hover:underline"
                            @click="openReplyFor = null"
                        >
                            Cancel
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 flex-wrap">
                    <button
                        v-if="!msg.is_spam && !msg.handled_at"
                        type="button"
                        class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline inline-flex items-center gap-1"
                        @click="markAsHandled(msg.id)"
                    >
                        <Flag class="w-3.5 h-3.5" />
                        <span>Mark handled</span>
                    </button>
                    <button
                        v-if="!msg.is_spam"
                        type="button"
                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1"
                        @click="toggleReply(msg.id)"
                    >
                        <Send class="w-3.5 h-3.5" />
                        <span>{{ openReplyFor === msg.id ? 'Close reply' : 'Reply' }}</span>
                    </button>
                    <button
                        v-if="!msg.read_at"
                        type="button"
                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1"
                        @click="markAsRead(msg.id)"
                    >
                        <Check class="w-3.5 h-3.5" />
                        <span>Mark as read</span>
                    </button>
                    <button
                        type="button"
                        class="text-xs font-semibold text-red-500 hover:underline inline-flex items-center gap-1"
                        @click="deleteMessage(msg.id)"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>

        <div
            v-else
            class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800"
        >
            <Mail class="w-10 h-10 text-slate-400 mx-auto mb-2" />
            <p class="text-slate-600 dark:text-slate-400 font-medium">No messages in this view.</p>
        </div>

        <Pagination :paginator="messages" />
    </div>
</template>