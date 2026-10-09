<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    permission: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: props.permission.name,
    guard_name: props.permission.guard_name,
})

const submit = () => {
    form.put(
        route('permissions.update', {
            permission: props.permission.id,
        }),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head title="Edit Permission" />

    <div class="mx-auto max-w-3xl">

        <!-- HEADER -->
        <div class="mb-6">
            <div class="mb-3">
                <Link
                    :href="route('permissions.index')"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>

                    Kembali
                </Link>
            </div>

            <h1
                class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
            >
                Edit Permission
            </h1>

            <p
                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
            >
                Perbarui informasi permission yang sudah terdaftar.
            </p>
        </div>

        <!-- FORM CARD -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >

            <!-- CARD HEADER -->
            <div
                class="border-b border-slate-200 px-6 py-5 dark:border-slate-700"
            >
                <h2
                    class="text-base font-semibold text-slate-900 dark:text-white"
                >
                    Informasi Permission
                </h2>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Ubah nama permission atau guard yang digunakan.
                </p>
            </div>

            <!-- FORM -->
            <form
                @submit.prevent="submit"
                class="p-6"
            >
                <div class="space-y-6">

                    <!-- NAMA PERMISSION -->
                    <div>
                        <label
                            for="name"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Nama Permission
                        </label>

                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="off"
                            placeholder="Contoh: user.view"
                            class="w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-2 dark:bg-slate-800 dark:text-white"
                            :class="
                                form.errors.name
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20 dark:border-red-500'
                                    : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 dark:border-slate-700'
                            "
                        />

                        <p
                            v-if="form.errors.name"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.name }}
                        </p>

                        <p
                            v-else
                            class="mt-2 text-xs text-slate-400"
                        >
                            Contoh format:

                            <span class="font-mono">
                                user.view
                            </span>,

                            <span class="font-mono">
                                user.create
                            </span>,

                            <span class="font-mono">
                                user.edit
                            </span>
                        </p>
                    </div>

                    <!-- GUARD NAME -->
                    <div>
                        <label
                            for="guard_name"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                        >
                            Guard Name
                        </label>

                        <select
                            id="guard_name"
                            v-model="form.guard_name"
                            class="w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:ring-2 dark:bg-slate-800 dark:text-white"
                            :class="
                                form.errors.guard_name
                                    ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20 dark:border-red-500'
                                    : 'border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 dark:border-slate-700'
                            "
                        >
                            <option value="web">
                                web
                            </option>
                        </select>

                        <p
                            v-if="form.errors.guard_name"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.guard_name }}
                        </p>
                    </div>
                </div>

                <!-- BUTTON -->
                <div
                    class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end dark:border-slate-700"
                >

                    <!-- BATAL -->
                    <Link
                        :href="route('permissions.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <!-- UPDATE -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                    >

                        <!-- LOADING -->
                        <svg
                            v-if="form.processing"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
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

                        <span>
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Update Permission'
                            }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>