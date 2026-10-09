<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },

    variant: {
        type: String,
        default: 'primary',
    },

    size: {
        type: String,
        default: 'md',
    },

    loading: {
        type: Boolean,
        default: false,
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition focus:outline-none focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50"
        :class="[
            {
                'px-4 py-2.5 text-sm': size === 'md',
                'px-3 py-2 text-xs': size === 'sm',
                'px-5 py-3 text-sm': size === 'lg',
            },

            variant === 'primary'
                ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/20 hover:bg-blue-700 focus:ring-blue-500/20'
                : '',

            variant === 'secondary'
                ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus:ring-slate-400/20 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700'
                : '',

            variant === 'danger'
                ? 'bg-red-600 text-white shadow-sm shadow-red-600/20 hover:bg-red-700 focus:ring-red-500/20'
                : '',

            variant === 'success'
                ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20 hover:bg-emerald-700 focus:ring-emerald-500/20'
                : '',

            variant === 'ghost'
                ? 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                : '',
        ]"
    >
        <svg
            v-if="loading"
            class="h-4 w-4 animate-spin"
            viewBox="0 0 24 24"
            fill="none"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
            />
        </svg>

        <slot />
    </button>
</template>