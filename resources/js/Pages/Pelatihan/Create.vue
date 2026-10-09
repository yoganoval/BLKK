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
    form.post(route('pelatihans.store'))
}
</script>

<template>
    <Head title="Tambah Pelatihan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        <div class="mx-auto max-w-4xl p-4 sm:p-6 lg:p-8">

            <!-- HEADER -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <Link
                        :href="route('pelatihans.index')"
                        class="transition hover:text-indigo-600 dark:hover:text-indigo-400"
                    >
                        Pelatihan
                    </Link>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>

                    <span>Tambah</span>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14v7"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                        >
                            Tambah Pelatihan
                        </h1>

                        <p
                            class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                        >
                            Tambahkan data pelatihan baru ke dalam sistem.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FORM CARD -->
            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <!-- CARD HEADER -->
                <div
                    class="border-b border-slate-200 px-6 py-5 dark:border-slate-800"
                >
                    <h2
                        class="text-base font-semibold text-slate-900 dark:text-white"
                    >
                        Informasi Pelatihan
                    </h2>

                    <p
                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                    >
                        Lengkapi informasi pelatihan di bawah ini.
                    </p>
                </div>

                <!-- FORM BODY -->
                <div class="space-y-6 p-6">

                    <!-- NAMA -->
                    <div>
                        <label
                            for="nama"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            Nama Pelatihan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="nama"
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Pelatihan Desain Grafis"
                            autofocus
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:bg-slate-800"
                            :class="{
                                'border-red-400 focus:border-red-500 focus:ring-red-500/10':
                                    form.errors.nama,
                            }"
                        />

                        <p
                            v-if="form.errors.nama"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <!-- KETERANGAN -->
                    <div>
                        <label
                            for="keterangan"
                            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                        >
                            Keterangan
                            <span class="font-normal text-slate-400">
                                (Opsional)
                            </span>
                        </label>

                        <textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            rows="5"
                            placeholder="Masukkan keterangan mengenai pelatihan..."
                            class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:bg-slate-800"
                            :class="{
                                'border-red-400 focus:border-red-500 focus:ring-red-500/10':
                                    form.errors.keterangan,
                            }"
                        ></textarea>

                        <p
                            v-if="form.errors.keterangan"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.keterangan }}
                        </p>
                    </div>
                </div>

                <!-- FOOTER -->
                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50/50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/20 sm:flex-row sm:justify-end"
                >
                    <Link
                        :href="route('pelatihans.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white"
                    >
                        Batal
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
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
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pelatihan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>