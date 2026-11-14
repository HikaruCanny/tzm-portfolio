<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = withDefaults(defineProps<{
    delay?: number;
}>(), {
    delay: 0,
});

const root = ref<HTMLElement | null>(null);
const visible = ref(false);
let observer: IntersectionObserver | null = null;

onMounted(() => {
    observer = new IntersectionObserver(([entry]) => {
        if (entry?.isIntersecting) {
            visible.value = true;
            observer?.disconnect();
        }
    }, {
        threshold: 0.25,
        rootMargin: '0px 0px -80px 0px',
    });

    if (root.value) {
        observer.observe(root.value);
    }
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <div
        ref="root"
        class="will-change-transform"
        :style="{
            opacity: visible ? 1 : 0,
            transform: visible ? 'none' : 'translateY(56px)',
            transition: 'opacity 0.8s cubic-bezier(0.22, 1, 0.36, 1), transform 0.8s cubic-bezier(0.22, 1, 0.36, 1)',
            transitionDelay: `${props.delay}ms`,
        }"
    >
        <slot />
    </div>
</template>
