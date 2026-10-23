<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

interface Role {
    id: number
    name: string
    guard_name: string
}

interface Permission {
    id: number
    name: string
    guard_name: string
}

const props = defineProps<{
    role: Role
    permissions: Permission[]
    rolePermissions: number[]
}>()

const form = useForm({
    permissions: props.rolePermissions.map(Number),
})

const isChecked = (permissionId: number) => {
    return form.permissions.includes(Number(permissionId))
}

const togglePermission = (permissionId: number) => {
    const id = Number(permissionId)

    if (isChecked(id)) {
        form.permissions = form.permissions.filter(
            permission => permission !== id,
        )
    } else {
        form.permissions.push(id)
    }
}

const selectAll = () => {
    form.permissions = props.permissions.map(
        permission => Number(permission.id),
    )
}

const deselectAll = () => {
    form.permissions = []
}

const submit = () => {
    form.put(
        route('rolepermissions.update', props.role.id),
        {
            preserveScroll: true,
        },
    )
}
</script>

<template>
    <Head :title="`Permission - ${role.name}`" />

    <div class="mb-6">
        <Link
            :href="route('rolepermissions.index')"
            class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-emerald-600 dark:text-slate-400"
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

        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Atur Permission
        </h1>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Atur hak akses untuk role
            <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                {{ role.name }}
            </span>
        </p>
    </div>

    <!-- INFORMASI ROLE -->
    <div
        class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
    >
        <div
            class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-xl font-bold uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
                >
                    {{ role.name?.charAt(0) ?? '?' }}
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ role.name }}
                    </h2>

                    <div class="mt-1 space-y-1 text-sm text-slate-500 dark:text-slate-400">
                        <p>Role ID: {{ role.id }}</p>

                        <p class="flex items-center gap-2">
                            Guard Name:
                            <span
                                class="rounded-md bg-blue-100 px-2 py-0.5 font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                            >
                                {{ role.guard_name }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-slate-100 px-5 py-3 dark:bg-slate-800">
                <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                    Permission dipilih
                </div>

                <div class="mt-1 text-xl font-bold text-slate-900 dark:text-white">
                    {{ form.permissions.length }}
                    <span class="text-sm font-normal text-slate-400">
                        / {{ permissions.length }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- DAFTAR PERMISSION -->
    <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
    >
        <div
            class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700"
        >
            <div>
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                    Daftar Permission
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pilih permission yang diberikan kepada role ini.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    @click="selectAll"
                    :disabled="form.processing || permissions.length === 0"
                    class="rounded-xl bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-200 disabled:opacity-50 dark:bg-emerald-900/30 dark:text-emerald-400"
                >
                    Pilih Semua
                </button>

                <button
                    type="button"
                    @click="deselectAll"
                    :disabled="form.processing || form.permissions.length === 0"
                    class="rounded-xl bg-red-100 px-4 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-200 disabled:opacity-50 dark:bg-red-900/30 dark:text-red-400"
                >
                    Hapus Semua
                </button>
            </div>
        </div>

        <div class="p-6">
            <div
                v-if="permissions.length > 0"
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <label
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="group flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-emerald-300 hover:bg-emerald-50 dark:border-slate-700 dark:hover:border-emerald-700 dark:hover:bg-emerald-900/10"
                    :class="{
                        'border-emerald-400 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/20':
                            isChecked(permission.id),
                    }"
                >
                    <input
                        type="checkbox"
                        :checked="isChecked(permission.id)"
                        :disabled="form.processing"
                        @change="togglePermission(permission.id)"
                        class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 disabled:opacity-50 dark:border-slate-600 dark:bg-slate-800"
                    />

                    <div class="min-w-0 flex-1">
                        <div
                            class="break-all text-sm font-semibold text-slate-800 dark:text-slate-200"
                        >
                            {{ permission.name }}
                        </div>

                        <div class="mt-1 text-xs text-slate-400">
                            ID: {{ permission.id }}
                        </div>

                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Guard: {{ permission.guard_name }}
                        </div>
                    </div>
                </label>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center dark:border-slate-700"
            >
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Belum ada permission
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Silakan tambahkan permission terlebih dahulu.
                </p>
            </div>
        </div>

        <!-- ERROR VALIDASI -->
        <div
            v-if="form.errors.permissions"
            class="mx-6 mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400"
        >
            {{ form.errors.permissions }}
        </div>

        <!-- FOOTER -->
        <div
            class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700 dark:bg-slate-800/30"
        >
            <Link
                :href="route('rolepermissions.index')"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
            >
                Batal
            </Link>

            <button
                type="button"
                @click="submit"
                :disabled="form.processing"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <svg
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
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

                <svg
                    v-else
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
                        d="M5 13l4 4L19 7"
                    />
                </svg>

                {{ form.processing ? 'Menyimpan...' : 'Simpan Permission' }}
            </button>
        </div>
    </div>
</template>
