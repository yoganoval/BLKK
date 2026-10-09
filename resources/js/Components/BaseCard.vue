<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },

    description: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
    >
        <div
            v-if="title || description || $slots.header"
            class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"
        >
            <slot name="header">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    {{ title }}
                </h3>

                <p
                    v-if="description"
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    {{ description }}
                </p>
            </slot>
        </div>

        <div class="p-5">
            <slot />
        </div>

        <div
            v-if="$slots.footer"
            class="border-t border-slate-200 px-5 py-4 dark:border-slate-800"
        >
            <slot name="footer" />
        </div>
    </div>
</template>