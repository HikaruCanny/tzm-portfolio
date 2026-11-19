<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import Pagination from '@/components/Pagination.vue';
import {
    Mail,
    Check,
    Trash2,
    Send,
    Flag,
    AlertTriangle,
    ArrowLeft,
    Inbox,
    MoreHorizontal,
    Archive,
    ArchiveRestore,
} from '@lucide/vue';
import { useDebounceFn } from '@vueuse/core';
import { computed, ref, watch } from 'vue';

defineOptions({ layout: AdminLayout });

type InboxMessage = {
    id: number;
    name: string;
    email: string;
    subject?: string | null;
    message: string;
    is_spam: boolean;
    read_at?: string | null;
    handled_at?: string | null;
    archived_at?: string | null;
    created_at: string;
    replies?: Array<{
        id: number;
        body: string;
        sent_at: string;
        user?: { name?: string };
    }>;
};

const props = defineProps<{
    messages: {
        data: InboxMessage[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    filter: 'all' | 'unread' | 'spam' | 'handled' | 'archived';
    search?: string;
}>();

const filters = [
    { key: 'all', label: 'All' },
    { key: 'unread', label: 'Unread' },
    { key: 'handled', label: 'Handled' },
    { key: 'spam', label: 'Spam' },
    { key: 'archived', label: 'Archived' },
] as const;

const selectedIds = ref<number[]>([]);
const selectedId = ref<number | null>(null);
const openReplyFor = ref(false);
const replyForm = useForm({ body: '' });

const selectedMessage = computed(() => props.messages.data.find((m) => m.id === selectedId.value) ?? null);

const pickDefaultSelection = (rows: InboxMessage[]) => {
    if (rows.length === 0) {
        selectedId.value = null;
        return;
    }

    if (selectedId.value && rows.some((m) => m.id === selectedId.value)) {
        return;
    }

    const firstUnread = rows.find((m) => !m.read_at && !m.is_spam);
    selectedId.value = (firstUnread ?? rows[0]).id;
};

watch(() => props.messages.data, (rows) => {
    pickDefaultSelection(rows);
    selectedIds.value = selectedIds.value.filter((id) => rows.some((m) => m.id === id));
}, { immediate: true });

const searchQuery = ref(props.search ?? '');

const reloadInbox = (overrides: { filter?: typeof props.filter; q?: string } = {}) => {
    const filterValue = overrides.filter ?? props.filter;
    const q = (overrides.q ?? searchQuery.value).trim();

    router.get(
        '/admin/messages',
        { filter: filterValue, ...(q ? { q } : {}) },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const applySearch = useDebounceFn(() => reloadInbox(), 350);

watch(searchQuery, () => applySearch());

const selectMessage = (id: number) => {
    selectedId.value = id;
    openReplyFor.value = false;
    replyForm.clearErrors();
    replyForm.reset();
};

const setFilter = (key: typeof props.filter) => {
    selectedId.value = null;
    openReplyFor.value = false;
    reloadInbox({ filter: key });
};

const clearSearch = () => {
    searchQuery.value = '';
};

const toggleSelected = (id: number) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter((rowId) => rowId !== id);
    } else {
        selectedIds.value = [...selectedIds.value, id];
    }
};

const selectAllOnPage = () => {
    selectedIds.value = props.messages.data.map((m) => m.id);
};

const clearSelection = () => {
    selectedIds.value = [];
};

const bulkAction = (action: 'read' | 'handled' | 'archive' | 'unarchive' | 'delete') => {
    if (selectedIds.value.length === 0) {
        return;
    }

    if (action === 'delete' && !confirm(`Delete ${selectedIds.value.length} message(s)?`)) {
        return;
    }

    router.post(
        '/admin/messages/bulk',
        { action, ids: selectedIds.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = [];
            },
        },
    );
};

const archiveMessage = (id: number) => {
    router.patch(`/admin/messages/${id}/archive`, {}, { preserveScroll: true });
};

const unarchiveMessage = (id: number) => {
    router.patch(`/admin/messages/${id}/unarchive`, {}, { preserveScroll: true });
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
        const next = props.messages.data.find((m) => m.id !== id);
        selectedId.value = next?.id ?? null;
        router.delete(`/admin/messages/${id}`);
    }
};

const startReply = () => {
    openReplyFor.value = true;
    replyForm.clearErrors();
    replyForm.reset();
};

const sendReply = () => {
    if (!selectedId.value) {
        return;
    }

    replyForm.post(`/admin/messages/${selectedId.value}/reply`, {
        preserveScroll: true,
        onSuccess: () => {
            openReplyFor.value = false;
            replyForm.reset();
        },
    });
};

const formatWhen = (iso: string) =>
    new Date(iso).toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const formatListWhen = (iso: string) => {
    const d = new Date(iso);
    const now = new Date();
    const sameDay =
        d.getDate() === now.getDate() &&
        d.getMonth() === now.getMonth() &&
        d.getFullYear() === now.getFullYear();

    if (sameDay) {
        return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
    }

    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
};

const previewLine = (msg: InboxMessage) => {
    const base = msg.subject?.trim() || msg.message.trim().replace(/\s+/g, ' ');

    return base.length > 72 ? `${base.slice(0, 72)}…` : base;
};

const initials = (name: string) =>
    name
        .trim()
        .split(/\s+/)
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();

const isUnread = (msg: InboxMessage) => !msg.read_at && !msg.is_spam;

const statusLabel = (msg: InboxMessage) => {
    if (msg.is_spam) {
        return 'Spam';
    }
    if (msg.handled_at) {
        return 'Handled';
    }
    if (msg.read_at) {
        return 'Read';
    }

    return 'New';
};
</script>

<template>
    <div class="mx-auto flex max-w-7xl flex-col gap-5">
        <Head title="Visitor Messages – TZM Admin" />

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                    <Inbox class="h-3.5 w-3.5" />
                    Inbox
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Messages</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ messages.total }} in this view · select a conversation on the left
                </p>
            </div>
            <button
                type="button"
                class="self-start rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:text-indigo-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-indigo-500 dark:hover:text-indigo-300"
                @click="markAllRead"
            >
                Mark all as read
            </button>
        </div>

        <!-- Search + filters -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div
                class="inline-flex flex-wrap gap-1 rounded-2xl border border-slate-200 bg-slate-100/80 p-1 dark:border-slate-800 dark:bg-slate-900/80"
            >
            <button
                v-for="item in filters"
                :key="item.key"
                type="button"
                class="rounded-xl px-4 py-2 text-xs font-semibold transition-all"
                :class="
                    filter === item.key
                        ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-800 dark:text-white'
                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200'
                "
                @click="setFilter(item.key)"
            >
                {{ item.label }}
            </button>
            </div>
            <div class="flex w-full max-w-md items-center gap-2 sm:justify-end">
                <input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Search name, email, subject…"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                />
                <button
                    v-if="searchQuery"
                    type="button"
                    class="shrink-0 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200"
                    @click="clearSearch"
                >
                    Clear
                </button>
            </div>
        </div>

        <div
            v-if="messages.data.length > 0"
            class="flex flex-wrap items-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50/80 px-4 py-2.5 dark:border-indigo-900 dark:bg-indigo-950/30"
        >
            <template v-if="selectedIds.length > 0">
                <span class="text-xs font-semibold text-indigo-900 dark:text-indigo-200">
                    {{ selectedIds.length }} selected
                </span>
                <button
                    v-if="filter !== 'archived'"
                    type="button"
                    class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm dark:bg-slate-900 dark:text-slate-200"
                    @click="bulkAction('read')"
                >
                    Mark read
                </button>
                <button
                    v-if="filter !== 'archived' && filter !== 'spam'"
                    type="button"
                    class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-emerald-700 shadow-sm dark:bg-slate-900 dark:text-emerald-400"
                    @click="bulkAction('handled')"
                >
                    Handled
                </button>
                <button
                    v-if="filter !== 'archived'"
                    type="button"
                    class="inline-flex items-center gap-1 rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm dark:bg-slate-900 dark:text-slate-200"
                    @click="bulkAction('archive')"
                >
                    <Archive class="h-3.5 w-3.5" />
                    Archive
                </button>
                <button
                    v-if="filter === 'archived'"
                    type="button"
                    class="inline-flex items-center gap-1 rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-indigo-700 shadow-sm dark:bg-slate-900 dark:text-indigo-300"
                    @click="bulkAction('unarchive')"
                >
                    <ArchiveRestore class="h-3.5 w-3.5" />
                    Restore
                </button>
                <button
                    type="button"
                    class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-red-600 shadow-sm dark:bg-slate-900 dark:text-red-400"
                    @click="bulkAction('delete')"
                >
                    Delete
                </button>
                <button type="button" class="text-xs font-semibold text-slate-500 hover:underline" @click="clearSelection">
                    Clear
                </button>
            </template>
            <template v-else>
                <span class="text-xs text-slate-500 dark:text-slate-400">Select messages for bulk actions</span>
                <button type="button" class="text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-400" @click="selectAllOnPage">
                    Select all on this page
                </button>
            </template>
        </div>

        <!-- Split inbox -->
        <div
            v-if="messages.data.length > 0"
            class="flex min-h-[min(72vh,680px)] flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-lg shadow-slate-200/40 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none lg:flex-row"
        >
            <!-- List -->
            <aside
                class="flex w-full flex-col border-b border-slate-200 dark:border-slate-800 lg:w-[min(100%,340px)] lg:shrink-0 lg:border-b-0 lg:border-r"
                :class="selectedId ? 'hidden lg:flex' : 'flex'"
            >
                <div class="border-b border-slate-100 px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:border-slate-800">
                    Conversations
                </div>
                <ul class="flex-1 divide-y divide-slate-100 overflow-y-auto dark:divide-slate-800">
                    <li v-for="msg in messages.data" :key="msg.id" class="flex items-stretch">
                        <label
                            class="flex cursor-pointer items-center border-r border-slate-100 px-3 dark:border-slate-800"
                            @click.stop
                        >
                            <input
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                :checked="selectedIds.includes(msg.id)"
                                @change="toggleSelected(msg.id)"
                            />
                        </label>
                        <button
                            type="button"
                            class="group flex min-w-0 flex-1 gap-3 px-4 py-3 text-left transition"
                            :class="
                                selectedId === msg.id
                                    ? 'bg-gradient-to-r from-indigo-50 to-transparent dark:from-indigo-950/50'
                                    : 'hover:bg-slate-50 dark:hover:bg-slate-800/40'
                            "
                            @click="selectMessage(msg.id)"
                        >
                            <div
                                class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-xs font-bold text-white shadow-inner"
                                :class="
                                    msg.is_spam
                                        ? 'from-amber-500 to-orange-600'
                                        : isUnread(msg)
                                          ? 'from-indigo-500 to-violet-600'
                                          : 'from-slate-400 to-slate-600'
                                "
                            >
                                {{ initials(msg.name) }}
                                <span
                                    v-if="isUnread(msg)"
                                    class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-indigo-500 dark:border-slate-900"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <span
                                        class="truncate text-sm font-semibold"
                                        :class="isUnread(msg) ? 'text-slate-900 dark:text-white' : 'text-slate-700 dark:text-slate-300'"
                                    >
                                        {{ msg.name }}
                                    </span>
                                    <span class="shrink-0 text-[11px] tabular-nums text-slate-400">{{ formatListWhen(msg.created_at) }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ previewLine(msg) }}
                                </p>
                                <span
                                    class="mt-1.5 inline-block rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                                    :class="
                                        msg.is_spam
                                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'
                                            : msg.handled_at
                                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                              : msg.read_at
                                                ? 'bg-slate-100 text-slate-500 dark:bg-slate-800'
                                                : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'
                                    "
                                >
                                    {{ statusLabel(msg) }}
                                </span>
                            </div>
                        </button>
                    </li>
                </ul>
            </aside>

            <!-- Detail -->
            <section
                class="flex min-h-0 flex-1 flex-col"
                :class="selectedId ? 'flex' : 'hidden lg:flex'"
            >
                <template v-if="selectedMessage">
                    <header class="flex items-start gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <button
                            type="button"
                            class="mt-1 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden dark:hover:bg-slate-800"
                            aria-label="Back to list"
                            @click="selectedId = null"
                        >
                            <ArrowLeft class="h-5 w-5" />
                        </button>
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-bold text-white"
                        >
                            {{ initials(selectedMessage.name) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-lg font-bold text-slate-900 dark:text-white">
                                {{ selectedMessage.subject || selectedMessage.name }}
                            </h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ selectedMessage.name }} ·
                                <a
                                    :href="`mailto:${selectedMessage.email}`"
                                    class="text-indigo-600 hover:underline dark:text-indigo-400"
                                >
                                    {{ selectedMessage.email }}
                                </a>
                            </p>
                            <p class="mt-1 text-xs text-slate-400">{{ formatWhen(selectedMessage.created_at) }}</p>
                        </div>
                        <span
                            v-if="selectedMessage.is_spam"
                            class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-200"
                        >
                            <AlertTriangle class="h-3.5 w-3.5" />
                            Spam
                        </span>
                    </header>

                    <div class="flex-1 space-y-4 overflow-y-auto px-5 py-5">
                        <article
                            class="rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white p-5 dark:border-slate-800 dark:from-slate-950/80 dark:to-slate-900"
                        >
                            <p
                                v-if="selectedMessage.subject"
                                class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Message
                            </p>
                            <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                {{ selectedMessage.message }}
                            </p>
                        </article>

                        <div
                            v-if="selectedMessage.replies?.length"
                            class="space-y-3"
                        >
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Sent replies</p>
                            <div
                                v-for="reply in selectedMessage.replies"
                                :key="reply.id"
                                class="ml-auto max-w-[92%] rounded-2xl rounded-br-md bg-indigo-600 px-4 py-3 text-sm text-white shadow-md shadow-indigo-600/20"
                            >
                                <p class="text-[11px] font-medium text-indigo-100">
                                    {{ reply.user?.name ?? 'You' }} · {{ formatWhen(reply.sent_at) }}
                                </p>
                                <p class="mt-1 whitespace-pre-wrap break-words">{{ reply.body }}</p>
                            </div>
                        </div>

                        <div v-if="openReplyFor && !selectedMessage.is_spam" class="space-y-2">
                            <textarea
                                v-model="replyForm.body"
                                rows="4"
                                class="w-full resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950"
                                placeholder="Type your reply…"
                            />
                            <p v-if="replyForm.errors.body" class="text-xs text-red-500">{{ replyForm.errors.body }}</p>
                        </div>
                    </div>

                    <footer
                        class="flex flex-wrap items-center gap-2 border-t border-slate-100 bg-slate-50/90 px-5 py-3 dark:border-slate-800 dark:bg-slate-950/50"
                    >
                        <template v-if="openReplyFor && !selectedMessage.is_spam">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-500 disabled:opacity-50"
                                :disabled="replyForm.processing"
                                @click="sendReply"
                            >
                                <Send class="h-3.5 w-3.5" />
                                Send reply
                            </button>
                            <button
                                type="button"
                                class="rounded-xl px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-200/80 dark:hover:bg-slate-800"
                                @click="openReplyFor = false"
                            >
                                Cancel
                            </button>
                        </template>
                        <template v-else>
                            <button
                                v-if="!selectedMessage.is_spam"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-indigo-500"
                                @click="startReply"
                            >
                                <Send class="h-3.5 w-3.5" />
                                Reply
                            </button>
                            <button
                                v-if="!selectedMessage.is_spam && !selectedMessage.handled_at"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:border-emerald-300 dark:border-slate-700 dark:bg-slate-900 dark:text-emerald-400"
                                @click="markAsHandled(selectedMessage.id)"
                            >
                                <Flag class="h-3.5 w-3.5" />
                                Handled
                            </button>
                            <button
                                v-if="!selectedMessage.read_at"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                @click="markAsRead(selectedMessage.id)"
                            >
                                <Check class="h-3.5 w-3.5" />
                                Mark read
                            </button>
                            <button
                                v-if="!selectedMessage.archived_at"
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                @click="archiveMessage(selectedMessage.id)"
                            >
                                <Archive class="h-3.5 w-3.5" />
                                Archive
                            </button>
                            <button
                                v-else
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 dark:border-slate-700 dark:bg-slate-900 dark:text-indigo-300"
                                @click="unarchiveMessage(selectedMessage.id)"
                            >
                                <ArchiveRestore class="h-3.5 w-3.5" />
                                Restore
                            </button>
                        </template>
                        <button
                            type="button"
                            class="ml-auto inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                            @click="deleteMessage(selectedMessage.id)"
                        >
                            <Trash2 class="h-3.5 w-3.5" />
                            Delete
                        </button>
                    </footer>
                </template>

                <!-- Empty detail -->
                <div
                    v-else
                    class="flex flex-1 flex-col items-center justify-center gap-3 px-6 text-center"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 dark:bg-slate-800"
                    >
                        <MoreHorizontal class="h-8 w-8 text-slate-400" />
                    </div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Select a message to read</p>
                    <p class="max-w-xs text-xs text-slate-400">Your inbox stays short — only one conversation open at a time.</p>
                </div>
            </section>
        </div>

        <div
            v-else
            class="rounded-3xl border border-dashed border-slate-200 py-16 text-center dark:border-slate-800"
        >
            <Mail class="mx-auto mb-3 h-10 w-10 text-slate-300 dark:text-slate-600" />
            <p class="font-medium text-slate-600 dark:text-slate-400">No messages in this view</p>
        </div>

        <Pagination v-if="messages.data.length > 0" :paginator="messages" />
    </div>
</template>