<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';

const message = computed(() => {
    const flash = usePage().props.flash as { success?: string | null } | undefined;

    return flash?.success || null;
});
</script>

<template>
    <div
        v-if="message"
        class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200"
        role="status"
    >
        <CheckCircle2 class="h-5 w-5 shrink-0" />
        <span>{{ message }}</span>
    </div>
</template>
