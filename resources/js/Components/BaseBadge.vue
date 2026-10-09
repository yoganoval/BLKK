<script setup>
defineProps({
    variant: {
        type: String,
        default: 'default',
    },
})
</script>

<template>
    <span
        class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold"
        :class="{
            'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300':
                variant === 'default',

            'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400':
                variant === 'primary',

            'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400':
                variant === 'success',

            'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400':
                variant === 'danger',

            'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400':
                variant === 'warning',
        }"
    >
        <slot />
    </span>
</template>