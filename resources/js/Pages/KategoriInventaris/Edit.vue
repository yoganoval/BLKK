<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    kategori: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    nama: props.kategori.nama ?? '',
    keterangan: props.kategori.keterangan ?? '',
})

const submit = () => {
    form.put(
        route('kategori-inventaris.update', props.kategori.id),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head :title="`Edit Kategori - ${kategori.nama}`" />

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

                <span>Edit</span>
            </div>

            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                Edit Kategori Inventaris
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Perbarui informasi kategori inventaris.
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
                            ID kategori #{{ kategori.id }}
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

                <!-- INFO ASET -->
                <div
                    class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 dark:border-indigo-500/20 dark:bg-indigo-500/5"
                >
                    <div class="flex items-start gap-3">
                        <svg
                            class="mt-0.5 h-5 w-5 shrink-0 text-indigo-600 dark:text-indigo-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                            />
                        </svg>

                        <div>
                            <p class="text-sm font-semibold text-indigo-900 dark:text-indigo-300">
                                Aset dalam kategori
                            </p>

                            <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-400">
                                Kategori ini memiliki
                                <strong>{{ kategori.inventaris?.length ?? kategori.inventaris_count ?? 0 }}</strong>
                                aset inventaris.
                            </p>
                        </div>
                    </div>
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

                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>

