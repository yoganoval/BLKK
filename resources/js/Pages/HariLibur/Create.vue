<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const form = useForm({
    tanggal: '',
    nama: '',
    keterangan: '',
    status: true,
})

const submit = () => {
    form.post(route('hari-libur.store'))
}
</script>

<template>
    <Head title="Tambah Hari Libur" />

    <div class="mx-auto max-w-3xl space-y-6">
        <!-- HEADER -->
        <div>
            <Link
                :href="route('hari-libur.index')"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                ← Kembali ke Hari Libur
            </Link>

            <h1
                class="mt-3 text-2xl font-bold text-slate-800 dark:text-white"
            >
                Tambah Hari Libur
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Tambahkan tanggal yang tidak dapat digunakan untuk absensi.
            </p>
        </div>

        <!-- FORM -->
        <form
            @submit.prevent="submit"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >
            <div class="space-y-6">
                <!-- TANGGAL -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Tanggal
                    </label>

                    <input
                        v-model="form.tanggal"
                        type="date"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    />

                    <p
                        v-if="form.errors.tanggal"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.tanggal }}
                    </p>
                </div>

                <!-- NAMA -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Nama Hari Libur
                    </label>

                    <input
                        v-model="form.nama"
                        type="text"
                        placeholder="Contoh: Libur Nasional"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    />

                    <p
                        v-if="form.errors.nama"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.nama }}
                    </p>
                </div>

                <!-- KETERANGAN -->
                <div>
                    <label
                        class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
                    >
                        Keterangan
                    </label>

                    <textarea
                        v-model="form.keterangan"
                        rows="4"
                        placeholder="Contoh: Libur dalam rangka peringatan..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    ></textarea>

                    <p
                        v-if="form.errors.keterangan"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.keterangan }}
                    </p>
                </div>

                <!-- STATUS -->
                <div
                    class="rounded-xl border border-slate-200 p-4 dark:border-slate-700"
                >
                    <label
                        class="flex cursor-pointer items-center justify-between"
                    >
                        <div>
                            <div
                                class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            >
                                Aktifkan Hari Libur
                            </div>

                            <div
                                class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                            >
                                Jika aktif, absensi akan dinonaktifkan pada
                                tanggal tersebut.
                            </div>
                        </div>

                        <input
                            v-model="form.status"
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800"
                        />
                    </label>
                </div>
            </div>

            <!-- BUTTON -->
            <div
                class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6 dark:border-slate-700"
            >
                <Link
                    :href="route('hari-libur.index')"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Hari Libur'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>