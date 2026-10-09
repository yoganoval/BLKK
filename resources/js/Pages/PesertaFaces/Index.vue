<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertaFaces: {
        type: Array,
        default: () => [],
    },
})

const openCreate = () => {
    router.get(route('peserta-faces.create'))
}

const openEdit = (id) => {
    router.get(route('peserta-faces.edit', id))
}

const openShow = (id) => {
    router.get(route('peserta-faces.show', id))
}

const deleteFace = (id) => {
    if (!confirm('Hapus data wajah peserta ini?')) {
        return
    }

    router.delete(
        route('peserta-faces.destroy', id),
        {
            preserveScroll: true,
        }
    )
}

const getPhotoUrl = (path) => {
    if (!path) {
        return null
    }

    return `/storage/${path}`
}

const getPendaftaran = (face) => {
    const pendaftarans = face.peserta?.pendaftarans ?? []

    return pendaftarans[0] ?? null
}

const getKelas = (face) => {
    const pendaftaran = getPendaftaran(face)

    return pendaftaran?.kelas?.nama ?? '-'
}

const getPelatihan = (face) => {
    const pendaftaran = getPendaftaran(face)

    return pendaftaran?.program_pelatihan?.pelatihan?.nama ?? '-'
}

const getTahap = (face) => {
    const pendaftaran = getPendaftaran(face)

    return pendaftaran?.program_pelatihan?.tahap?.nama ?? '-'
}

/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/
const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
}
</script>

<template>
    <Head title="Data Wajah Peserta" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-xl font-bold text-slate-800 dark:text-white"
                >
                    Data Wajah Peserta
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Kelola data wajah peserta untuk kebutuhan presensi
                    berbasis face recognition.
                </p>
            </div>

            <button
                type="button"
                @click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
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

                Tambah Data Wajah
            </button>
        </div>

        <!-- STATISTIK -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            <!-- TOTAL -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="flex items-center gap-4">

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 0l-4.553 2.276A1 1 0 013 15.382V8.618a1 1 0 011.447-.894L9 10m0 4l6-4m-6 4v6m6-10v10"
                            />
                        </svg>
                    </div>

                    <div>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Total Data Wajah
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-slate-800 dark:text-white"
                        >
                            {{ pesertaFaces.length }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- EMBEDDING -->
            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="flex items-center gap-4">

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
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
                    </div>

                    <div>
                        <p
                            class="text-sm text-slate-500 dark:text-slate-400"
                        >
                            Embedding Terdaftar
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-slate-800 dark:text-white"
                        >
                            {{
                                pesertaFaces.filter(
                                    face => face.face_embedding
                                ).length
                            }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >

            <!-- TABLE HEADER -->
            <div
                class="border-b border-slate-200 px-6 py-4 dark:border-slate-700"
            >
                <h2
                    class="font-semibold text-slate-800 dark:text-white"
                >
                    Daftar Data Wajah
                </h2>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Data wajah peserta yang telah terdaftar.
                </p>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">

                <table
                    class="min-w-full divide-y divide-slate-200 dark:divide-slate-700"
                >

                    <!-- HEADER -->
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/50"
                    >
                        <tr>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                No
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Foto
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Peserta
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Kelas
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Pelatihan
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Tahap
                            </th>

                            <th
                                class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Status
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Terdaftar
                            </th>

                            <th
                                class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody
                        class="divide-y divide-slate-200 dark:divide-slate-700"
                    >

                        <!-- DATA -->
                        <tr
                            v-for="(face, index) in pesertaFaces"
                            :key="face.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >

                            <!-- NO -->
                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ index + 1 }}
                            </td>

                            <!-- FOTO -->
                            <td class="px-5 py-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800"
                                >

                                    <img
                                        v-if="getPhotoUrl(face.photo_path)"
                                        :src="getPhotoUrl(face.photo_path)"
                                        alt="Foto wajah"
                                        class="h-full w-full object-cover"
                                    />

                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-7 w-7 text-slate-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>

                                </div>
                            </td>

                            <!-- PESERTA -->
                            <td class="px-5 py-4">
                                <div>

                                    <p
                                        class="text-sm font-semibold text-slate-800 dark:text-white"
                                    >
                                        {{ face.peserta?.nama ?? '-' }}
                                    </p>

                                </div>
                            </td>

                            <!-- KELAS -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    {{ getKelas(face) }}
                                </span>
                            </td>

                            <!-- PELATIHAN -->
                            <td class="px-5 py-4">
                                <span
                                    class="text-sm font-medium text-slate-700 dark:text-slate-300"
                                >
                                    {{ getPelatihan(face) }}
                                </span>
                            </td>

                            <!-- TAHAP -->
                            <td class="px-5 py-4">
                                <span
                                    class="text-sm font-medium text-slate-700 dark:text-slate-300"
                                >
                                    {{ getTahap(face) }}
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4 text-center">

                                <span
                                    v-if="face.face_embedding"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                    ></span>

                                    Terdaftar
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-amber-500"
                                    ></span>

                                    Belum Ada
                                </span>

                            </td>

                            <!-- TANGGAL -->
                            <td class="px-5 py-4">
                                <span
                                    class="text-sm text-slate-600 dark:text-slate-300"
                                >
                                    {{ formatDate(face.registered_at) }}
                                </span>
                            </td>

                            <!-- AKSI -->
                            <td class="px-5 py-4">

                                <div
                                    class="flex justify-center gap-2"
                                >

                                    <!-- SHOW -->
                                    <button
                                        type="button"
                                        @click="openShow(face.id)"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-indigo-400"
                                        title="Lihat"
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
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </button>

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        @click="openEdit(face.id)"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-indigo-400"
                                        title="Edit"
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
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                            />
                                        </svg>
                                    </button>

                                    <!-- DELETE -->
                                    <button
                                        type="button"
                                        @click="deleteFace(face.id)"
                                        class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                        title="Hapus"
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
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0V5a2 2 0 012-2h2a2 2 0 012 2v2m-7 0h10"
                                            />
                                        </svg>
                                    </button>

                                </div>

                            </td>

                        </tr>

                        <!-- EMPTY -->
                        <tr v-if="pesertaFaces.length === 0">

                            <td
                                colspan="9"
                                class="px-5 py-12 text-center"
                            >

                                <div
                                    class="flex flex-col items-center justify-center"
                                >

                                    <div
                                        class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-slate-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542-7-1.274-4.057-5.065-7-9.542-7z"
                                            />
                                        </svg>
                                    </div>

                                    <h3
                                        class="mt-4 text-sm font-semibold text-slate-700 dark:text-slate-300"
                                    >
                                        Belum ada data wajah
                                    </h3>

                                    <p
                                        class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                                    >
                                        Silakan tambahkan data wajah peserta.
                                    </p>

                                    <button
                                        type="button"
                                        @click="openCreate"
                                        class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                    >
                                        Tambah Data Wajah
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>
        </div>

    </div>
</template>