<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    permissions: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    name: '',
    guard_name: 'web',
    permissions: [],
})

const submit = () => {
    form.post(route('roles.store'))
}

const togglePermission = (permissionId) => {
    if (form.permissions.includes(permissionId)) {
        form.permissions = form.permissions.filter(
            (id) => id !== permissionId,
        )
    } else {
        form.permissions.push(permissionId)
    }
}
</script>

<template>
    <Head title="Tambah Role" />

    <AuthenticatedLayout>
        <!-- HEADER -->
        <template #header>
            <div>
                <h2
                    class="text-xl font-semibold text-gray-800 dark:text-gray-100"
                >
                    Tambah Role
                </h2>

                <p
                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                >
                    Tambahkan role baru dan tentukan hak aksesnya
                </p>
            </div>
        </template>

        <!-- CONTENT -->
        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

                <!-- FORM CARD -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- CARD HEADER -->
                    <div
                        class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3
                                    class="text-base font-semibold text-gray-800 dark:text-gray-100"
                                >
                                    Informasi Role
                                </h3>

                                <p
                                    class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Isi informasi dasar role terlebih dahulu.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FORM -->
                    <form
                        @submit.prevent="submit"
                        class="space-y-6 p-6"
                    >
                        <!-- NAMA ROLE -->
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Nama Role
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Contoh: Admin, Petugas, Anggota"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                            />

                            <p
                                v-if="form.errors.name"
                                class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- GUARD -->
                        <div>
                            <label
                                for="guard_name"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Guard Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="guard_name"
                                v-model="form.guard_name"
                                type="text"
                                class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-700 outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            />

                            <p
                                class="mt-1.5 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Gunakan <strong>web</strong> untuk autentikasi
                                pengguna Laravel.
                            </p>

                            <p
                                v-if="form.errors.guard_name"
                                class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.guard_name }}
                            </p>
                        </div>

                        <!-- PERMISSIONS -->
                        <div>
                            <div
                                class="mb-3 flex items-center justify-between"
                            >
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Permissions
                                    </label>

                                    <p
                                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Pilih hak akses yang dimiliki role ini.
                                    </p>
                                </div>

                                <span
                                    class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-400"
                                >
                                    {{ form.permissions.length }} dipilih
                                </span>
                            </div>

                            <div
                                v-if="permissions.length"
                                class="grid gap-3 sm:grid-cols-2"
                            >
                                <label
                                    v-for="permission in permissions"
                                    :key="permission.id"
                                    class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-500 dark:hover:bg-blue-900/10"
                                >
                                    <input
                                        type="checkbox"
                                        :value="permission.id"
                                        :checked="
                                            form.permissions.includes(
                                                permission.id,
                                            )
                                        "
                                        @change="
                                            togglePermission(permission.id)
                                        "
                                        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                    />

                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-800 dark:text-gray-100"
                                        >
                                            {{ permission.name }}
                                        </p>

                                        <p
                                            class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ permission.guard_name }}
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <div
                                v-else
                                class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center dark:border-gray-600"
                            >
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Belum ada permission tersedia.
                                </p>
                            </div>

                            <p
                                v-if="form.errors.permissions"
                                class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.permissions }}
                            </p>
                        </div>

                        <!-- ACTION -->
                        <div
                            class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 dark:border-gray-700 sm:flex-row sm:justify-end"
                        >
                            <Link
                                :href="route('roles.index')"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
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
                                    class="h-5 w-5"
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

                                {{
                                    form.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Role'
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

