```vue
<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const form = useForm({
    nama: '',
    deskripsi: '',
    durasi: '',
    // biaya: '',
    status: 'aktif',
})

const submit = () => {
    form.post(route('pelatihans.store'))
}
</script>

<template>
    <div class="min-h-full p-4 sm:p-6">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <Link
                    :href="route('pelatihans.index')"
                    class="hover:text-blue-600 dark:hover:text-blue-400"
                >
                    Pelatihan
                </Link>

                <span>/</span>

                <span class="text-gray-700 dark:text-gray-300">
                    Tambah
                </span>
            </div>

            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                Tambah Pelatihan
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tambahkan data pelatihan baru
            </p>
        </div>

        <!-- Form -->
        <div
            class="max-w-4xl rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <form @submit.prevent="submit">

                <!-- Form Header -->
                <div
                    class="border-b border-gray-200 px-6 py-5 dark:border-gray-700"
                >
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        Informasi Pelatihan
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Isi informasi pelatihan dengan lengkap.
                    </p>
                </div>

                <!-- Form Body -->
                <div class="space-y-5 p-6">

                    <!-- Nama -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Nama Pelatihan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Pelatihan Komputer Dasar"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                            :class="{
                                'border-red-500 dark:border-red-500':
                                    form.errors.nama,
                            }"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            v-model="form.deskripsi"
                            rows="4"
                            placeholder="Masukkan deskripsi pelatihan..."
                            class="w-full resize-none rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                            :class="{
                                'border-red-500 dark:border-red-500':
                                    form.errors.deskripsi,
                            }"
                        ></textarea>

                        <p
                            v-if="form.errors.deskripsi"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.deskripsi }}
                        </p>
                    </div>

                    <!-- Durasi & Biaya -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <!-- Durasi -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Durasi
                            </label>

                            <div class="relative">
                                <input
                                    v-model="form.durasi"
                                    type="number"
                                    min="1"
                                    placeholder="Contoh: 5"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-16 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            form.errors.durasi,
                                    }"
                                />

                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400"
                                >
                                    Hari
                                </span>
                            </div>

                            <p
                                v-if="form.errors.durasi"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.durasi }}
                            </p>
                        </div>

                        <!-- Biaya -->
                        <!-- <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Biaya
                            </label>

                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Rp
                                </span>

                                <input
                                    v-model="form.biaya"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            form.errors.biaya,
                                    }"
                                />
                            </div>

                            <p
                                v-if="form.errors.biaya"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.biaya }}
                            </p>
                        </div> -->
                    </div>

                    <!-- Status -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Status
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="selesai">Selesai</option>
                        </select>

                        <p
                            v-if="form.errors.status"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex flex-col-reverse gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:justify-end dark:border-gray-700"
                >
                    <Link
                        :href="route('pelatihans.index')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg
                            v-if="form.processing"
                            class="mr-2 h-4 w-4 animate-spin"
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

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pelatihan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
```
