<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Head title="Role Permission" />

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Role Permission
        </h1>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Atur hak akses permission berdasarkan role.
        </p>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-700">
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">
            Daftar Role
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pilih role untuk mengatur permission.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        #
                    </th>

                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Role
                    </th>

                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Permission
                    </th>

                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr
                    v-for="(role, index) in props.roles"
                    :key="role.id"
                    class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                >
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        {{ index + 1 }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 font-bold uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                {{ role.name?.charAt(0) ?? '?' }}
                            </div>

                            <div>
                                <div class="font-semibold capitalize text-slate-900 dark:text-white">
                                    {{ role.name }}
                                </div>

                                <div class="text-xs text-slate-400">
                                    Role ID: {{ role.id }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            {{ role.permissions_count ?? 0 }}
                            Permission
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right">
                        <Link
                            :href="route('rolepermissions.edit', role.id)"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md"
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
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                />
                            </svg>

                            Atur Akses
                        </Link>
                    </td>
                </tr>

                <tr v-if="props.roles.length === 0">
                    <td colspan="4" class="px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            Belum ada role
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Silakan buat role terlebih dahulu.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</template>
