<template>
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
        :width="size" :height="size">
        <circle cx="50" cy="50" r="37" :stroke="ringColor" stroke-width="8"/>
        <circle cx="38" cy="38" r="4.4" :fill="ringColor"/>
        <circle cx="62" cy="38" r="4.4" :fill="ringColor"/>
        <circle cx="38" cy="62" r="4.4" :fill="ringColor"/>
        <circle cx="62" cy="62" r="4.4" :fill="ringColor"/>
        <line x1="38" y1="38" x2="62" y2="62" :stroke="threadColor" stroke-width="6.5" stroke-linecap="round"/>
        <line x1="62" y1="38" x2="38" y2="62" :stroke="threadColor" stroke-width="6.5" stroke-linecap="round"/>
    </svg>
</template>

<script setup>
    import { computed } from 'vue'

    const props = defineProps({
        size: {
            type: [Number, String],
            default: 32
        },
        // "on-paper" (pine ring on light backgrounds) or "on-pine" (cream ring on dark backgrounds)
        variant: {
            type: String,
            default: 'on-paper'
        }
    })

    const ringColor = computed(() => props.variant === 'on-pine' ? '#EEF0E8' : '#1B3A2B')
    const threadColor = '#C9922B'
</script>
