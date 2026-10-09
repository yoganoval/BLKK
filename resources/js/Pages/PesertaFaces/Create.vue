<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertas: {
        type: Array,
        default: () => [],
    },
})

const selectedPeserta = ref(null)

const video = ref(null)
const canvas = ref(null)

const stream = ref(null)

const cameraReady = ref(false)
const cameraLoading = ref(false)
const capturing = ref(false)

const previewUrl = ref(null)

const errors = ref({})

/*
|--------------------------------------------------------------------------
| Buka Kamera
|--------------------------------------------------------------------------
*/

const startCamera = async () => {
    cameraLoading.value = true

    try {
        if (!navigator.mediaDevices?.getUserMedia) {
            alert(
                'Browser tidak mendukung akses kamera.'
            )

            return
        }

        stream.value = await navigator.mediaDevices.getUserMedia({
            video: {
                width: {
                    ideal: 1280,
                },

                height: {
                    ideal: 720,
                },

                facingMode: 'user',
            },

            audio: false,
        })

        if (video.value) {
            video.value.srcObject = stream.value

            await video.value.play()

            cameraReady.value = true
        }

    } catch (error) {
        console.error(
            'Camera error:',
            error
        )

        if (error.name === 'NotAllowedError') {
            alert(
                'Akses kamera ditolak. Silakan izinkan kamera pada browser.'
            )
        } else if (error.name === 'NotFoundError') {
            alert(
                'Kamera tidak ditemukan pada perangkat.'
            )
        } else {
            alert(
                'Kamera tidak dapat dibuka.'
            )
        }

    } finally {
        cameraLoading.value = false
    }
}


/*
|--------------------------------------------------------------------------
| Tutup Kamera
|--------------------------------------------------------------------------
*/

const stopCamera = () => {
    if (!stream.value) {
        return
    }

    stream.value
        .getTracks()
        .forEach(track => {
            track.stop()
        })

    stream.value = null

    cameraReady.value = false
}


/*
|--------------------------------------------------------------------------
| Pilih Peserta
|--------------------------------------------------------------------------
*/

const selectPeserta = (peserta) => {
    selectedPeserta.value = peserta

    errors.value = {}

    if (previewUrl.value) {
        URL.revokeObjectURL(
            previewUrl.value
        )

        previewUrl.value = null
    }
}


/*
|--------------------------------------------------------------------------
| Ambil Foto Webcam
|--------------------------------------------------------------------------
*/

const capturePhoto = () => {
    if (!selectedPeserta.value) {
        alert(
            'Silakan pilih peserta terlebih dahulu.'
        )

        return
    }

    if (!video.value || !canvas.value) {
        return
    }

    if (!cameraReady.value) {
        alert(
            'Kamera belum siap.'
        )

        return
    }

    capturing.value = true

    const width =
        video.value.videoWidth

    const height =
        video.value.videoHeight

    if (!width || !height) {
        capturing.value = false

        alert(
            'Video belum siap. Silakan tunggu beberapa saat.'
        )

        return
    }

    canvas.value.width = width
    canvas.value.height = height

    const context =
        canvas.value.getContext('2d')

    /*
    |--------------------------------------------------------------------------
    | Mirror image
    |--------------------------------------------------------------------------
    |
    | Webcam biasanya tampil mirror.
    | Foto yang disimpan juga dibuat mirror
    | agar sama dengan preview kamera.
    |
    */

    context.save()

    context.translate(width, 0)

    context.scale(-1, 1)

    context.drawImage(
        video.value,
        0,
        0,
        width,
        height
    )

    context.restore()

    canvas.value.toBlob(
        (blob) => {
            if (!blob) {
                capturing.value = false

                alert(
                    'Gagal mengambil foto.'
                )

                return
            }

            /*
            |--------------------------------------------------------------------------
            | Preview
            |--------------------------------------------------------------------------
            */

            if (previewUrl.value) {
                URL.revokeObjectURL(
                    previewUrl.value
                )
            }

            previewUrl.value =
                URL.createObjectURL(blob)

            /*
            |--------------------------------------------------------------------------
            | Upload
            |--------------------------------------------------------------------------
            */

            uploadPhoto(blob)
        },
        'image/jpeg',
        0.90
    )
}


/*
|--------------------------------------------------------------------------
| Upload Foto
|--------------------------------------------------------------------------
*/

const uploadPhoto = (blob) => {
    const formData =
        new FormData()

    const file =
        new File(
            [blob],
            `face-${selectedPeserta.value.id}-${Date.now()}.jpg`,
            {
                type: 'image/jpeg',
            }
        )

    formData.append(
        'peserta_id',
        selectedPeserta.value.id
    )

    formData.append(
        'photo',
        file
    )

    errors.value = {}

    router.post(
        route(
            'peserta-faces.store'
        ),
        formData,
        {
            forceFormData: true,

            preserveScroll: true,

            onSuccess: () => {
                capturing.value = false

                /*
                |--------------------------------------------------------------------------
                | Kembali ke halaman index
                |--------------------------------------------------------------------------
                */

                router.get(
                    route(
                        'peserta-faces.index'
                    )
                )
            },

            onError: (validationErrors) => {
                capturing.value = false

                errors.value =
                    validationErrors

                console.error(
                    'Validation error:',
                    validationErrors
                )

                if (
                    validationErrors.image
                ) {
                    alert(
                        validationErrors.image
                    )
                }
            },

            onFinish: () => {
                capturing.value = false
            },
        }
    )
}


/*
|--------------------------------------------------------------------------
| Kembali
|--------------------------------------------------------------------------
*/

const goBack = () => {
    router.get(
        route(
            'peserta-faces.index'
        )
    )
}


/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/

onMounted(() => {
    startCamera()
})

onBeforeUnmount(() => {
    stopCamera()

    if (previewUrl.value) {
        URL.revokeObjectURL(
            previewUrl.value
        )
    }
})
</script>


<template>
    <div class="min-h-screen bg-slate-50 p-4 transition-colors duration-300 dark:bg-slate-950 sm:p-6 lg:p-8">

        <!-- ===================================================== -->
        <!-- HEADER -->
        <!-- ===================================================== -->
        <div class="mx-auto mb-6 max-w-7xl">
            <div
                class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">

                    <!-- ICON -->
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
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
                                d="M3 7h4l2-3h6l2 3h4a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"
                            />
                            <circle cx="12" cy="13" r="4" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800 dark:text-white sm:text-2xl">
                            Registrasi Wajah
                        </h1>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Daftarkan wajah peserta untuk kebutuhan sistem presensi.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="goBack"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
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
                </button>
            </div>
        </div>


        <!-- ===================================================== -->
        <!-- MAIN CONTENT -->
        <!-- ===================================================== -->
        <div class="mx-auto grid max-w-7xl gap-6 xl:grid-cols-12">

            <!-- ================================================= -->
            <!-- PESERTA -->
            <!-- ================================================= -->
            <div class="xl:col-span-4">

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >

                    <!-- CARD HEADER -->
                    <div
                        class="border-b border-slate-100 px-5 py-4 dark:border-slate-800"
                    >
                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                            >
                                1
                            </div>

                            <div>
                                <h2 class="font-semibold text-slate-800 dark:text-white">
                                    Pilih Peserta
                                </h2>

                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    Pilih peserta yang akan didaftarkan
                                </p>
                            </div>

                        </div>
                    </div>


                    <!-- SEARCH / INFO -->
                    <div class="px-5 pt-5">
                        <div
                            class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>

                            <span class="text-xs text-slate-400 dark:text-slate-500">
                                {{ pesertas.length }} peserta tersedia
                            </span>
                        </div>
                    </div>


                    <!-- LIST -->
                    <div class="max-h-[520px] space-y-2 overflow-y-auto p-5">

                        <button
                            v-for="peserta in pesertas"
                            :key="peserta.id"
                            type="button"
                            @click="selectPeserta(peserta)"
                            class="group relative w-full rounded-xl border p-3.5 text-left transition-all duration-200"
                            :class="
                                selectedPeserta?.id === peserta.id
                                    ? 'border-indigo-500 bg-indigo-50 shadow-sm dark:border-indigo-500/60 dark:bg-indigo-500/10'
                                    : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600 dark:hover:bg-slate-800'
                            "
                        >

                            <div class="flex items-center gap-3">

                                <!-- AVATAR -->
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-sm font-bold"
                                    :class="
                                        selectedPeserta?.id === peserta.id
                                            ? 'bg-indigo-600 text-white'
                                            : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'
                                    "
                                >
                                    {{ peserta.nama?.charAt(0)?.toUpperCase() }}
                                </div>


                                <!-- INFO -->
                                <div class="min-w-0 flex-1">

                                    <div
                                        class="truncate text-sm font-semibold"
                                        :class="
                                            selectedPeserta?.id === peserta.id
                                                ? 'text-indigo-700 dark:text-indigo-400'
                                                : 'text-slate-700 dark:text-slate-200'
                                        "
                                    >
                                        {{ peserta.nama }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                        No. Induk: {{ peserta.no_induk }}
                                    </div>

                                    <div
                                        v-if="peserta.kelas"
                                        class="mt-1 text-xs text-slate-400 dark:text-slate-500"
                                    >
                                        Kelas: {{ peserta.kelas.nama }}
                                    </div>

                                </div>


                                <!-- CHECK -->
                                <div
                                    v-if="selectedPeserta?.id === peserta.id"
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>

                            </div>

                        </button>


                        <!-- EMPTY -->
                        <div
                            v-if="pesertas.length === 0"
                            class="rounded-xl border border-dashed border-slate-300 p-8 text-center dark:border-slate-700"
                        >
                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-slate-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"
                                    />
                                    <circle cx="9" cy="7" r="4" />
                                </svg>
                            </div>

                            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                                Belum ada peserta
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Data peserta belum tersedia.
                            </p>
                        </div>

                    </div>
                </div>
            </div>


            <!-- ================================================= -->
            <!-- CAMERA -->
            <!-- ================================================= -->
            <div class="xl:col-span-8">

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >

                    <!-- CAMERA HEADER -->
                    <div
                        class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                            >
                                2
                            </div>

                            <div>
                                <h2 class="font-semibold text-slate-800 dark:text-white">
                                    Ambil Foto Wajah
                                </h2>

                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    Pastikan wajah terlihat jelas
                                </p>
                            </div>

                        </div>


                        <!-- STATUS -->
                        <div
                            class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold"
                            :class="
                                cameraReady
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'
                            "
                        >
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="
                                    cameraReady
                                        ? 'animate-pulse bg-emerald-500'
                                        : 'bg-amber-500'
                                "
                            ></span>

                            {{ cameraReady ? 'Kamera Aktif' : 'Kamera Tidak Aktif' }}
                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <!-- SELECTED PARTICIPANT -->
                        <div
                            v-if="selectedPeserta"
                            class="mb-5 overflow-hidden rounded-xl border border-indigo-100 bg-indigo-50/70 dark:border-indigo-500/20 dark:bg-indigo-500/10"
                        >
                            <div class="flex items-center gap-3 p-4">

                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 font-bold text-white"
                                >
                                    {{ selectedPeserta.nama?.charAt(0)?.toUpperCase() }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400">
                                        Peserta terpilih
                                    </p>

                                    <p class="truncate text-sm font-bold text-indigo-900 dark:text-indigo-200">
                                        {{ selectedPeserta.nama }}
                                    </p>

                                    <p class="text-xs text-indigo-600 dark:text-indigo-400">
                                        No. Induk: {{ selectedPeserta.no_induk }}
                                    </p>
                                </div>

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 shrink-0 text-indigo-500"
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
                        </div>


                        <!-- CAMERA -->
                        <div
                            class="group relative overflow-hidden rounded-2xl bg-slate-950 shadow-xl ring-1 ring-black/10"
                        >

                            <video
                                ref="video"
                                autoplay
                                playsinline
                                muted
                                class="aspect-video w-full object-cover"
                                style="transform: scaleX(-1)"
                            ></video>


                            <!-- CAMERA GRADIENT -->
                            <div
                                class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/70 to-transparent"
                            ></div>


                            <!-- FACE GUIDE -->
                            <div
                                v-if="cameraReady"
                                class="pointer-events-none absolute inset-0 flex items-center justify-center"
                            >

                                <div
                                    class="relative h-[72%] w-[38%] min-w-[170px] max-w-[270px] rounded-[50%] border-2 border-white/80 shadow-[0_0_0_9999px_rgba(0,0,0,0.18)]"
                                >

                                    <!-- CENTER LINE -->
                                    <div
                                        class="absolute left-1/2 top-1/2 w-full -translate-x-1/2 border-t border-dashed border-white/40"
                                    ></div>

                                    <!-- TOP -->
                                    <div
                                        class="absolute left-1/2 top-[-2px] h-8 w-16 -translate-x-1/2 border-t-4 border-white"
                                    ></div>

                                </div>

                                <div class="absolute bottom-5 rounded-full bg-black/50 px-4 py-2 text-xs font-medium text-white backdrop-blur-sm">
                                    Posisikan wajah di dalam area
                                </div>

                            </div>


                            <!-- LOADING -->
                            <div
                                v-if="cameraLoading"
                                class="absolute inset-0 flex items-center justify-center bg-slate-950/90 backdrop-blur-sm"
                            >
                                <div class="text-center text-white">

                                    <div
                                        class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-white/20 border-t-white"
                                    ></div>

                                    <p class="text-sm font-semibold">
                                        Membuka kamera...
                                    </p>

                                    <p class="mt-1 text-xs text-white/50">
                                        Mohon tunggu sebentar
                                    </p>

                                </div>
                            </div>


                            <!-- ERROR -->
                            <div
                                v-if="!cameraLoading && !cameraReady"
                                class="absolute inset-0 flex items-center justify-center bg-slate-950/90 backdrop-blur-sm"
                            >

                                <div class="text-center text-white">

                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-3xl"
                                    >
                                        📷
                                    </div>

                                    <p class="font-semibold">
                                        Kamera belum aktif
                                    </p>

                                    <p class="mt-1 mb-5 text-xs text-white/50">
                                        Aktifkan kamera untuk mengambil foto
                                    </p>

                                    <button
                                        type="button"
                                        @click="startCamera"
                                        class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-lg transition hover:bg-slate-100"
                                    >
                                        Aktifkan Kamera
                                    </button>

                                </div>

                            </div>

                        </div>


                        <!-- HIDDEN CANVAS -->
                        <canvas
                            ref="canvas"
                            class="hidden"
                        ></canvas>


                        <!-- TIPS -->
                        <div
                            class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/40"
                        >
                            <div class="flex gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-500 dark:bg-amber-500/10"
                                >
                                    💡
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                        Tips mengambil foto
                                    </p>

                                    <div class="mt-2 grid gap-2 text-xs text-slate-500 dark:text-slate-400 sm:grid-cols-2">

                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-500">✓</span>
                                            Hadapkan wajah ke kamera
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-500">✓</span>
                                            Pastikan wajah terlihat penuh
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-500">✓</span>
                                            Gunakan pencahayaan cukup
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-500">✓</span>
                                            Lepaskan masker
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- ACTION -->
                        <div class="mt-5">

                            <button
                                type="button"
                                @click="capturePhoto"
                                :disabled="
                                    !cameraReady ||
                                    !selectedPeserta ||
                                    capturing
                                "
                                class="group flex w-full items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition-all duration-200 hover:bg-indigo-700 hover:shadow-indigo-600/30 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none"
                            >

                                <svg
                                    v-if="!capturing"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 transition-transform group-hover:scale-110"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 7h4l2-3h6l2 3h4a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"
                                    />
                                    <circle cx="12" cy="13" r="4" />
                                </svg>

                                <svg
                                    v-else
                                    class="h-5 w-5 animate-spin"
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
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>

                                <span>
                                    {{ capturing
                                        ? 'Menyimpan foto...'
                                        : 'Ambil & Simpan Foto'
                                    }}
                                </span>

                            </button>

                            <p
                                v-if="!selectedPeserta"
                                class="mt-2 text-center text-xs text-amber-500"
                            >
                                Pilih peserta terlebih dahulu
                            </p>

                        </div>


                        <!-- ERROR -->
                        <div
                            v-if="errors.image"
                            class="mt-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400"
                        >
                            <span class="text-lg">⚠️</span>

                            <div>
                                <p class="font-semibold">
                                    Gagal menyimpan foto
                                </p>

                                <p class="mt-1 text-xs">
                                    {{ errors.image }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>


        <!-- ===================================================== -->
        <!-- PREVIEW -->
        <!-- ===================================================== -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-3 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
        >
            <div
                v-if="previewUrl"
                class="mx-auto mt-6 max-w-7xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4 dark:border-slate-800"
                >
                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"
                        >
                            ✓
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-800 dark:text-white">
                                Foto Berhasil Diambil
                            </h2>

                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Preview foto wajah peserta
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex justify-center bg-slate-50 p-6 dark:bg-slate-950/40">

                    <div
                        class="overflow-hidden rounded-2xl border-4 border-white bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900"
                    >
                        <img
                            :src="previewUrl"
                            alt="Preview foto wajah"
                            class="max-h-[450px] max-w-full object-contain"
                        />
                    </div>

                </div>

            </div>
        </Transition>

    </div>
</template>
