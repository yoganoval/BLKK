<script setup>
defineProps({
    collapsed: {
        type: Boolean,
        default: false,
    },
})
</script>

<template>
    <div class="flex items-center gap-3">
        <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-600/20"
        >
            <img
                src="/storage/images/logo.png"
                alt="BLK Multimedia"
                class="h-8 w-8 object-contain"
            />
        </div>

        <div v-if="!collapsed" class="min-w-0">
            <h1 class="truncate text-base font-bold text-slate-900 dark:text-white">
                BLK Multimedia
            </h1>

            <p class="truncate text-[10px] text-slate-500 dark:text-slate-400">
                Balai Latihan Kerja Komunitas
            </p>
        </div>
    </div>
</template>