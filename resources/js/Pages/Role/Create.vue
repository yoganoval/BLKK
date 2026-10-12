<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

const form = useForm({
    name: '',
    guard_name: 'web',
});

const submit = () => {
    form.post(route('roles.store'));
};
</script>

<template>
    <Head title="Tambah Role" />

    <div class="space-y-6 p-4 sm:p-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Tambah Role
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tambahkan role dan tentukan guard untuk akun pengguna.
            </p>
        </div>

        <!-- Form -->
        <div
            class="mx-auto max-w-2xl rounded-xl border border-gray-200
                   bg-white p-6 shadow-sm dark:border-gray-700
                   dark:bg-gray-900"
        >
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Nama Role -->
                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-medium
                               text-gray-700 dark:text-gray-300"
                    >
                        Nama Role
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        placeholder="Contoh: admin, instruktur, peserta"
                        class="w-full rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm text-gray-900
                               outline-none focus:border-blue-500
                               focus:ring-2 focus:ring-blue-200
                               dark:border-gray-700 dark:bg-gray-800
                               dark:text-white"
                    />

                    <p
                        v-if="form.errors.name"
                        class="mt-2 text-sm text-red-500"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Guard Name -->
                <div>
                    <label
                        for="guard_name"
                        class="mb-2 block text-sm font-medium
                               text-gray-700 dark:text-gray-300"
                    >
                        Guard Name
                    </label>

                    <select
                        id="guard_name"
                        v-model="form.guard_name"
                        required
                        class="w-full rounded-lg border border-gray-300
                               bg-white px-4 py-2.5 text-sm text-gray-900
                               outline-none focus:border-blue-500
                               focus:ring-2 focus:ring-blue-200
                               dark:border-gray-700 dark:bg-gray-800
                               dark:text-white"
                    >
                        <option value="web">
                            Web — Admin, Pengelola, Instruktur
                        </option>

                        <option value="peserta">
                            Peserta — Akun tabel peserta
                        </option>

                        <option value="api">
                            API — Autentikasi API
                        </option>
                    </select>

                    <p
                        v-if="form.errors.guard_name"
                        class="mt-2 text-sm text-red-500"
                    >
                        {{ form.errors.guard_name }}
                    </p>

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Pilih guard sesuai jenis akun yang akan menggunakan
                        role ini.
                    </p>
                </div>

                <!-- Actions -->
                <div
                    class="flex flex-col-reverse gap-3 border-t
                           border-gray-200 pt-5 sm:flex-row
                           sm:justify-end dark:border-gray-700"
                >
                    <Link
                        :href="route('roles.index')"
                        class="inline-flex items-center justify-center
                               rounded-lg border border-gray-300 px-4 py-2.5
                               text-sm font-medium text-gray-700
                               hover:bg-gray-100 dark:border-gray-600
                               dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center
                               rounded-lg bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-blue-700
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >
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
</template>
