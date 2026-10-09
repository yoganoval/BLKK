<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const form = useForm({
    nama: '',
    keterangan: '',
})

const submit = () => {
    form.post(route('kategori-inventaris.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tambah Kategori Inventaris" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <Link
                    :href="route('kategori-inventaris.index')"
                    class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                >
                    Kategori Inventaris
                </Link>

                <span>/</span>

                <span>Tambah</span>
            </div>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Tambah Kategori Inventaris
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tambahkan kategori baru untuk mengelompokkan aset BLKK.
            </p>
        </div>

        <!-- FORM -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/10"
                    >
                        <svg
                            class="h-5 w-5 text-indigo-600 dark:text-indigo-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Informasi Kategori
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Isi informasi kategori inventaris.
                        </p>
                    </div>
                </div>
            </div>

            <form
                class="space-y-6 p-6"
                @submit.prevent="submit"
            >

                <!-- NAMA -->
                <div>
                    <label class="label">
                        Nama Kategori
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        v-model="form.nama"
                        type="text"
                        class="input"
                        placeholder="Contoh: Peralatan Multimedia"
                        autofocus
                    />

                    <p
                        v-if="form.errors.nama"
                        class="error"
                    >
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label class="label">
                        Keterangan
                    </label>

                    <textarea
                        v-model="form.keterangan"
                        rows="5"
                        class="input"
                        placeholder="Masukkan keterangan kategori..."
                    ></textarea>

                    <p
                        v-if="form.errors.keterangan"
                        class="error"
                    >
                        {{ form.errors.keterangan }}
                    </p>
                </div>

                <!-- BUTTON -->
                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end dark:border-gray-700">
                    <Link
                        :href="route('kategori-inventaris.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
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
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>
