<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },

    label: {
        type: String,
        default: '',
    },

    placeholder: {
        type: String,
        default: '',
    },

    type: {
        type: String,
        default: 'text',
    },

    error: {
        type: String,
        default: '',
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div>
        <label
            v-if="label"
            class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
        >
            {{ label }}
        </label>

        <input
            :value="modelValue"
            :type="type"
            :placeholder="placeholder"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            class="h-11 w-full rounded-xl border bg-white px-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950 dark:text-white"
            :class="
                error
                    ? 'border-red-400 focus:border-red-500 focus:ring-red-500/10'
                    : 'border-slate-200 focus:border-blue-500 focus:ring-blue-500/10 dark:border-slate-700'
            "
        />

        <p
            v-if="error"
            class="mt-1.5 text-xs font-medium text-red-500"
        >
            {{ error }}
        </p>
    </div>
</template>