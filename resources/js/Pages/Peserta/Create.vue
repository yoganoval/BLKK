<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const form = useForm({
    nama: '',
    alamat: '',
    kamar: '',
    lembaga: '',
    username: '',
    password: '',
    password_confirmation: '',
    is_active: true,
})

const submit = () => {
    form.post(route('pesertas.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Peserta" />

    <div class="min-h-screen bg-slate-50 px-4 py-6 dark:bg-slate-950 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">

            <!-- HEADER -->
            <div class="mb-6">
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <Link
                        :href="route('pesertas.index')"
                        class="transition hover:text-indigo-600"
                    >
                        Master Data
                    </Link>

                    <span>/</span>

                    <span>Peserta</span>

                    <span>/</span>

                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        Tambah
                    </span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-white">
                    Tambah Peserta
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan data peserta dan buat akun untuk login ke sistem.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- DATA PESERTA -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
                        <h2 class="font-semibold text-slate-800 dark:text-white">
                            Data Peserta
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Masukkan identitas peserta pelatihan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

                        <!-- NAMA -->
                        <div class="sm:col-span-2">
                            <label
                                for="nama"
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nama"
                                v-model="form.nama"
                                type="text"
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap peserta"
                                required
                                autofocus
                                :class="[
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                    form.errors.nama
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                ]"
                            />

                            <p v-if="form.errors.nama" class="mt-1.5 text-sm text-red-500">
                                {{ form.errors.nama }}
                            </p>
                        </div>

                        <!-- ALAMAT -->
                        <div class="sm:col-span-2">
                            <label
                                for="alamat"
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Alamat
                            </label>

                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                rows="3"
                                autocomplete="street-address"
                                placeholder="Masukkan alamat peserta"
                                :class="[
                                    'w-full resize-y rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                    form.errors.alamat
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                ]"
                            />

                            <p v-if="form.errors.alamat" class="mt-1.5 text-sm text-red-500">
                                {{ form.errors.alamat }}
                            </p>
                        </div>

                        <!-- KAMAR -->
                        <div>
                            <label
                                for="kamar"
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Kamar
                            </label>

                            <input
                                id="kamar"
                                v-model="form.kamar"
                                type="text"
                                placeholder="Contoh: A-12"
                                :class="[
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                    form.errors.kamar
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                ]"
                            />

                            <p v-if="form.errors.kamar" class="mt-1.5 text-sm text-red-500">
                                {{ form.errors.kamar }}
                            </p>
                        </div>

                        <!-- LEMBAGA -->
                        <div>
                            <label
                                for="lembaga"
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Lembaga
                            </label>

                            <input
                                id="lembaga"
                                v-model="form.lembaga"
                                type="text"
                                placeholder="Masukkan nama lembaga"
                                :class="[
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                    form.errors.lembaga
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                ]"
                            />

                            <p v-if="form.errors.lembaga" class="mt-1.5 text-sm text-red-500">
                                {{ form.errors.lembaga }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AKUN LOGIN -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
                        <h2 class="font-semibold text-slate-800 dark:text-white">
                            Akun Login
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Buat username dan password yang digunakan peserta untuk masuk ke sistem.
                        </p>
                    </div>

                    <div class="space-y-5 p-5 sm:p-6">

                        <!-- USERNAME -->
                        <div>
                            <label
                                for="username"
                                class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Username <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="username"
                                v-model="form.username"
                                type="text"
                                autocomplete="username"
                                placeholder="Masukkan username"
                                required
                                :class="[
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                    form.errors.username
                                        ? 'border-red-500 focus:border-red-500'
                                        : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                ]"
                            />

                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                                Gunakan huruf, angka, tanda hubung, atau garis bawah.
                            </p>

                            <p v-if="form.errors.username" class="mt-1.5 text-sm text-red-500">
                                {{ form.errors.username }}
                            </p>
                        </div>

                        <!-- PASSWORD -->
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>
                                <label
                                    for="password"
                                    class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                >
                                    Password <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    :class="[
                                        'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 dark:bg-slate-800 dark:text-white',
                                        form.errors.password
                                            ? 'border-red-500 focus:border-red-500'
                                            : 'border-slate-200 focus:border-indigo-500 dark:border-slate-700'
                                    ]"
                                />

                                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-500">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- KONFIRMASI PASSWORD -->
                            <div>
                                <label
                                    for="password_confirmation"
                                    class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                >
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Ulangi password"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>
                        </div>

                        <!-- STATUS AKUN -->
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />

                                <span>
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-white">
                                        Aktifkan akun peserta
                                    </span>

                                    <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">
                                        Peserta yang akunnya aktif dapat login sesuai hak akses yang diberikan.
                                    </span>
                                </span>
                            </label>

                            <p v-if="form.errors.is_active" class="mt-2 text-sm text-red-500">
                                {{ form.errors.is_active }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BUTTON -->
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link
                        :href="route('pesertas.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="form.processing"
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
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Peserta' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
