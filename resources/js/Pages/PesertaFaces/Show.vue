<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertaFace: {
        type: Object,
        required: true,
    },
})

/*
|--------------------------------------------------------------------------
| DATA PESERTA
|--------------------------------------------------------------------------
*/

const peserta = computed(() => {
    return props.pesertaFace?.peserta ?? null
})

const namaPeserta = computed(() => {
    return (
        peserta.value?.nama_lengkap ??
        peserta.value?.nama ??
        'Peserta'
    )
})

/*
|--------------------------------------------------------------------------
| FOTO
|--------------------------------------------------------------------------
*/

const photoUrl = computed(() => {
    const path =
        props.pesertaFace?.photo_path ??
        props.pesertaFace?.foto ??
        props.pesertaFace?.image ??
        null

    if (!path) {
        return null
    }

    if (
        path.startsWith('http://') ||
        path.startsWith('https://')
    ) {
        return path
    }

    if (path.startsWith('/storage/')) {
        return path
    }

    return `/storage/${path}`
})

/*
|--------------------------------------------------------------------------
| EMBEDDING
|--------------------------------------------------------------------------
*/

const embedding = computed(() => {
    let value =
        props.pesertaFace?.face_embedding

    if (!value) {
        return []
    }

    /*
     * Jika Laravel mengirim JSON string,
     * ubah menjadi array.
     */
    if (typeof value === 'string') {
        try {
            value = JSON.parse(value)
        } catch (error) {
            console.error(
                'Gagal membaca face embedding:',
                error
            )

            return []
        }
    }

    return Array.isArray(value)
        ? value
        : []
})

const embeddingDimension = computed(() => {
    return (
        props.pesertaFace?.embedding_dimension ??
        embedding.value.length ??
        0
    )
})

const hasEmbedding = computed(() => {
    return embedding.value.length > 0
})

/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    try {
        return new Intl.DateTimeFormat(
            'id-ID',
            {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            }
        ).format(new Date(date))
    } catch {
        return date
    }
}
</script>

<template>
    <Head
        :title="`Detail Wajah - ${namaPeserta}`"
    />

    <div
        class="min-h-screen bg-slate-50 px-4 py-6
               dark:bg-slate-950 sm:px-6 lg:px-8"
    >
        <div class="mx-auto max-w-5xl">

            <!-- HEADER -->
            <div class="mb-6">
                <div
                    class="flex flex-col gap-4
                           sm:flex-row sm:items-center
                           sm:justify-between"
                >
                    <div>
                        <div class="mb-2 flex items-center gap-2">

                            <div
                                class="flex h-10 w-10 items-center
                                       justify-center rounded-xl
                                       bg-indigo-100
                                       text-indigo-600
                                       dark:bg-indigo-500/10
                                       dark:text-indigo-400"
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
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                            </div>

                            <span
                                class="text-sm font-medium
                                       text-indigo-600
                                       dark:text-indigo-400"
                            >
                                Manajemen Wajah
                            </span>
                        </div>

                        <h1
                            class="text-2xl font-bold
                                   tracking-tight
                                   text-slate-900
                                   dark:text-white
                                   sm:text-3xl"
                        >
                            Detail Registrasi Wajah
                        </h1>

                        <p
                            class="mt-1 text-sm
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Informasi data wajah peserta
                            yang telah terdaftar.
                        </p>
                    </div>

                    <div
                        class="flex flex-col gap-2
                               sm:flex-row"
                    >
                        <Link
                            :href="
                                route(
                                    'peserta-faces.index'
                                )
                            "
                            class="inline-flex items-center
                                   justify-center gap-2
                                   rounded-xl border
                                   border-slate-200
                                   bg-white px-4 py-2.5
                                   text-sm font-semibold
                                   text-slate-700
                                   shadow-sm transition
                                   hover:bg-slate-50
                                   dark:border-slate-700
                                   dark:bg-slate-900
                                   dark:text-slate-200
                                   dark:hover:bg-slate-800"
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
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />
                            </svg>

                            Kembali
                        </Link>

                        <Link
                            :href="
                                route(
                                    'peserta-faces.edit',
                                    props.pesertaFace.id
                                )
                            "
                            class="inline-flex items-center
                                   justify-center gap-2
                                   rounded-xl
                                   bg-indigo-600 px-4 py-2.5
                                   text-sm font-bold
                                   text-white shadow-sm
                                   transition
                                   hover:bg-indigo-700"
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
                                    d="M11 5h2m-1-1v12m-4-3h8"
                                />
                            </svg>

                            Edit
                        </Link>
                    </div>
                </div>
            </div>

            <!-- MAIN -->
            <div
                class="grid gap-6
                       lg:grid-cols-5"
            >

                <!-- FOTO -->
                <div class="lg:col-span-2">

                    <div
                        class="overflow-hidden rounded-2xl
                               border border-slate-200
                               bg-white shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <div class="p-5">
                            <h2
                                class="text-base font-bold
                                       text-slate-900
                                       dark:text-white"
                            >
                                Foto Wajah
                            </h2>

                            <p
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                Foto yang digunakan sebagai
                                data registrasi wajah.
                            </p>
                        </div>

                        <div
                            class="mx-5 mb-5 overflow-hidden
                                   rounded-2xl border
                                   border-slate-200
                                   bg-slate-100
                                   dark:border-slate-700
                                   dark:bg-slate-800"
                        >
                            <img
                                v-if="photoUrl"
                                :src="photoUrl"
                                :alt="`Foto ${namaPeserta}`"
                                class="aspect-square w-full
                                       object-cover"
                            />

                            <div
                                v-else
                                class="flex aspect-square
                                       items-center
                                       justify-center"
                            >
                                <div class="text-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mx-auto h-16 w-16
                                               text-slate-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a2 2 0 01-2 1.788H5a2 2 0 01-2-2V8a2 2 0 012-2h4l2-2h2l2 2h4a2 2 0 012 2v8.618a2 2 0 01-1.447 1.894L15 14"
                                        />
                                    </svg>

                                    <p
                                        class="mt-3 text-sm
                                               font-medium
                                               text-slate-500
                                               dark:text-slate-400"
                                    >
                                        Tidak ada foto
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- STATUS -->
                        <div class="border-t
                                    border-slate-200 p-5
                                    dark:border-slate-800"
                        >
                            <div
                                class="flex items-center
                                       justify-between"
                            >
                                <span
                                    class="text-sm font-medium
                                           text-slate-600
                                           dark:text-slate-400"
                                >
                                    Status
                                </span>

                                <span
                                    class="inline-flex
                                           items-center gap-1.5
                                           rounded-full
                                           bg-emerald-100
                                           px-3 py-1
                                           text-xs font-bold
                                           text-emerald-700
                                           dark:bg-emerald-500/10
                                           dark:text-emerald-400"
                                >
                                    <span
                                        class="h-1.5 w-1.5
                                               rounded-full
                                               bg-emerald-500"
                                    ></span>

                                    Terdaftar
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAIL -->
                <div class="lg:col-span-3">

                    <!-- DATA PESERTA -->
                    <div
                        class="rounded-2xl border
                               border-slate-200
                               bg-white p-5 shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <h2
                            class="text-base font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            Data Peserta
                        </h2>

                        <p
                            class="mt-1 text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Informasi peserta pemilik data wajah.
                        </p>

                        <div class="mt-5 space-y-4">

                            <!-- NAMA -->
                            <div>
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Nama Peserta
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{ namaPeserta }}
                                </p>
                            </div>

                            <!-- NO INDUK -->
                            <div
                                v-if="peserta?.no_induk"
                            >
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    No. Induk
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{ peserta.no_induk }}
                                </p>
                            </div>

                            <!-- KELAS -->
                            <div
                                v-if="peserta?.kelas"
                            >
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Kelas
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{
                                        peserta.kelas.nama ??
                                        '-'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FACE EMBEDDING -->
                    <div
                        class="mt-6 rounded-2xl border
                               border-slate-200
                               bg-white p-5 shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <div
                            class="flex items-start
                                   justify-between gap-4"
                        >
                            <div>
                                <h2
                                    class="text-base font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    Face Embedding
                                </h2>

                                <p
                                    class="mt-1 text-xs
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Data numerik yang digunakan
                                    untuk proses pengenalan wajah.
                                </p>
                            </div>

                            <span
                                v-if="hasEmbedding"
                                class="shrink-0 rounded-full
                                       bg-emerald-100 px-3 py-1
                                       text-xs font-bold
                                       text-emerald-700
                                       dark:bg-emerald-500/10
                                       dark:text-emerald-400"
                            >
                                Tersedia
                            </span>

                            <span
                                v-else
                                class="shrink-0 rounded-full
                                       bg-red-100 px-3 py-1
                                       text-xs font-bold
                                       text-red-700
                                       dark:bg-red-500/10
                                       dark:text-red-400"
                            >
                                Tidak tersedia
                            </span>
                        </div>

                        <!-- EMBEDDING INFO -->
                        <div
                            class="mt-5 grid gap-3
                                   sm:grid-cols-2"
                        >
                            <div
                                class="rounded-xl border
                                       border-slate-200
                                       bg-slate-50 p-4
                                       dark:border-slate-700
                                       dark:bg-slate-800"
                            >
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Model
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{
                                        pesertaFace.model ??
                                        'Facenet512'
                                    }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl border
                                       border-slate-200
                                       bg-slate-50 p-4
                                       dark:border-slate-700
                                       dark:bg-slate-800"
                            >
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Dimensi Embedding
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{ embeddingDimension }}
                                </p>
                            </div>
                        </div>

                        <!-- EMBEDDING STATUS -->
                        <div
                            class="mt-4 rounded-xl border
                                   border-indigo-100
                                   bg-indigo-50 p-4
                                   dark:border-indigo-500/20
                                   dark:bg-indigo-500/10"
                        >
                            <div class="flex gap-3">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-5 w-5
                                           shrink-0
                                           text-indigo-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                                    />
                                </svg>

                                <div>
                                    <p
                                        class="text-sm font-semibold
                                               text-indigo-800
                                               dark:text-indigo-300"
                                    >
                                        Data wajah siap digunakan
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5
                                               text-indigo-700
                                               dark:text-indigo-400"
                                    >
                                        Embedding disimpan secara
                                        aman di database dan digunakan
                                        untuk proses pencocokan wajah
                                        pada sistem presensi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TANGGAL -->
                    <div
                        class="mt-6 rounded-2xl border
                               border-slate-200
                               bg-white p-5 shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <h2
                            class="text-base font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            Informasi Registrasi
                        </h2>

                        <div
                            class="mt-4 grid gap-4
                                   sm:grid-cols-2"
                        >
                            <div>
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    ID Registrasi
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    #{{ pesertaFace.id }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-medium
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Terdaftar Pada
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    {{
                                        formatDate(
                                            pesertaFace.registered_at ??
                                            pesertaFace.created_at
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM ACTION -->
            <div
                class="mt-6 flex flex-col-reverse
                       gap-3 sm:flex-row
                       sm:justify-end"
            >
                <Link
                    :href="
                        route(
                            'peserta-faces.index'
                        )
                    "
                    class="inline-flex items-center
                           justify-center gap-2
                           rounded-xl border
                           border-slate-200
                           bg-white px-5 py-3
                           text-sm font-semibold
                           text-slate-700 shadow-sm
                           transition
                           hover:bg-slate-50
                           dark:border-slate-700
                           dark:bg-slate-900
                           dark:text-slate-200
                           dark:hover:bg-slate-800"
                >
                    Kembali ke Daftar
                </Link>

                <Link
                    :href="
                        route(
                            'peserta-faces.edit',
                            pesertaFace.id
                        )
                    "
                    class="inline-flex items-center
                           justify-center gap-2
                           rounded-xl
                           bg-indigo-600 px-6 py-3
                           text-sm font-bold
                           text-white shadow-sm
                           transition
                           hover:bg-indigo-700"
                >
                    Edit Data Wajah
                </Link>
            </div>
        </div>
    </div>
</template>
