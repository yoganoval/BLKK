```vue
<script setup>
import { reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pelatihan: {
        type: Object,
        required: true,
    },
})

const form = reactive({
    nama: props.pelatihan?.nama ?? '',
    deskripsi: props.pelatihan?.deskripsi ?? '',
    durasi: props.pelatihan?.durasi ?? '',
    status: props.pelatihan?.status ?? 'aktif',
})

const errors = reactive({})

const submit = () => {
    router.put(
        route('pelatihans.update', props.pelatihan.id),
        form,
        {
            onError: (serverErrors) => {
                Object.assign(errors, serverErrors)
            },
        }
    )
}
</script>

<template>
    <div class="min-h-full p-4 sm:p-6">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Edit Pelatihan
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui informasi pelatihan
                </p>
            </div>

            <Link
                :href="route('pelatihans.index')"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                ← Kembali
            </Link>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-3xl">
            <div
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Nama -->
                    <div>
                        <label
                            for="nama"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Nama Pelatihan
                        </label>

                        <input
                            id="nama"
                            v-model="form.nama"
                            type="text"
                            placeholder="Masukkan nama pelatihan"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                    errors.nama
                            }"
                        />

                        <p
                            v-if="errors.nama"
                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.nama }}
                        </p>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label
                            for="deskripsi"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="4"
                            placeholder="Masukkan deskripsi pelatihan"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                    errors.deskripsi
                            }"
                        ></textarea>

                        <p
                            v-if="errors.deskripsi"
                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.deskripsi }}
                        </p>
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label
                            for="durasi"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Durasi (Hari)
                        </label>

                        <input
                            id="durasi"
                            v-model="form.durasi"
                            type="number"
                            min="1"
                            placeholder="Contoh: 30"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                    errors.durasi
                            }"
                        />

                        <p
                            v-if="errors.durasi"
                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.durasi }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label
                            for="status"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            :class="{
                                'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                    errors.status
                            }"
                        >
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="selesai">Selesai</option>
                        </select>

                        <p
                            v-if="errors.status"
                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                        >
                            {{ errors.status }}
                        </p>
                    </div>

                    <!-- Button -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end dark:border-gray-700"
                    >
                        <Link
                            :href="route('pelatihans.index')"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Batal
                        </Link>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
```
