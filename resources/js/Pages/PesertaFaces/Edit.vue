<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    pesertaFace: {
        type: Object,
        required: true,
    },

    pesertas: {
        type: Array,
        default: () => [],
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const selectedPeserta = ref(
    props.pesertaFace?.peserta_id ??
    props.pesertaFace?.peserta?.id ??
    ''
)

const video = ref(null)
const canvas = ref(null)

const cameraReady = ref(false)
const cameraLoading = ref(false)
const cameraError = ref('')

const previewUrl = ref(null)
const capturedFile = ref(null)

const processing = ref(false)
const localErrors = ref({})

let stream = null

/*
|--------------------------------------------------------------------------
| FOTO LAMA
|--------------------------------------------------------------------------
*/

const oldPhotoUrl = () => {
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
}

/*
|--------------------------------------------------------------------------
| PESERTA
|--------------------------------------------------------------------------
*/

const selectPeserta = (event) => {
    selectedPeserta.value = event.target.value

    if (localErrors.value.peserta_id) {
        delete localErrors.value.peserta_id
    }
}

/*
|--------------------------------------------------------------------------
| CAMERA
|--------------------------------------------------------------------------
*/

const startCamera = async () => {
    cameraLoading.value = true
    cameraError.value = ''

    try {
        stopCamera()

        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {
            throw new Error(
                'Browser tidak mendukung akses kamera.'
            )
        }

        stream =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 1280,
                    },
                    height: {
                        ideal: 720,
                    },
                },
                audio: false,
            })

        if (video.value) {
            video.value.srcObject = stream

            await video.value.play()
        }

        cameraReady.value = true
    } catch (error) {
        console.error(
            'Camera error:',
            error
        )

        cameraError.value =
            error?.message ??
            'Kamera tidak dapat digunakan. Pastikan browser sudah mendapatkan izin kamera.'

        cameraReady.value = false
    } finally {
        cameraLoading.value = false
    }
}

const stopCamera = () => {
    if (stream) {
        stream
            .getTracks()
            .forEach((track) => track.stop())

        stream = null
    }

    if (video.value) {
        video.value.srcObject = null
    }

    cameraReady.value = false
}

/*
|--------------------------------------------------------------------------
| CAPTURE PHOTO
|--------------------------------------------------------------------------
*/

const capturePhoto = () => {
    cameraError.value = ''

    if (!video.value || !canvas.value) {
        cameraError.value =
            'Kamera belum siap.'
        return
    }

    const width = video.value.videoWidth
    const height = video.value.videoHeight

    if (!width || !height) {
        cameraError.value =
            'Kamera belum siap. Silakan tunggu sebentar.'
        return
    }

    canvas.value.width = width
    canvas.value.height = height

    const context =
        canvas.value.getContext('2d')

    if (!context) {
        cameraError.value =
            'Gagal memproses gambar.'
        return
    }

    context.save()

    // Mirror agar hasil sesuai tampilan kamera
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
                cameraError.value =
                    'Gagal mengambil foto.'
                return
            }

            capturedFile.value =
                new File(
                    [blob],
                    `wajah-${Date.now()}.jpg`,
                    {
                        type: 'image/jpeg',
                    }
                )

            if (previewUrl.value) {
                URL.revokeObjectURL(
                    previewUrl.value
                )
            }

            previewUrl.value =
                URL.createObjectURL(blob)

            stopCamera()
        },
        'image/jpeg',
        0.9
    )
}

/*
|--------------------------------------------------------------------------
| RETAKE PHOTO
|--------------------------------------------------------------------------
*/

const retakePhoto = async () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(
            previewUrl.value
        )

        previewUrl.value = null
    }

    capturedFile.value = null
    cameraError.value = ''

    await startCamera()
}

/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

const submit = () => {
    localErrors.value = {}

    if (!selectedPeserta.value) {
        localErrors.value.peserta_id =
            'Silakan pilih peserta terlebih dahulu.'

        return
    }

    processing.value = true

    const formData = new FormData()

    formData.append(
        'peserta_id',
        selectedPeserta.value
    )

    /*
    |--------------------------------------------------------------------------
    | FOTO BARU
    |--------------------------------------------------------------------------
    |
    | Hanya kirim jika user mengambil foto baru.
    |
    | Laravel akan:
    | 1. menerima foto
    | 2. mengirim ke Python
    | 3. Python membuat embedding Facenet512
    | 4. menyimpan embedding baru
    |
    */

    if (capturedFile.value) {
        formData.append(
            'photo',
            capturedFile.value
        )
    }

    /*
    |--------------------------------------------------------------------------
    | METHOD PUT
    |--------------------------------------------------------------------------
    */

    formData.append(
        '_method',
        'PUT'
    )

    router.post(
        route(
            'peserta-faces.update',
            props.pesertaFace.id
        ),
        formData,
        {
            forceFormData: true,

            preserveScroll: true,

            onStart: () => {
                processing.value = true
            },

            onError: (errors) => {
                console.error(
                    'Update face error:',
                    errors
                )

                localErrors.value = errors
            },

            onSuccess: () => {
                /*
                |--------------------------------------------------------------------------
                | TIDAK PERLU router.get()
                |--------------------------------------------------------------------------
                |
                | Controller Laravel sudah melakukan:
                |
                | return redirect()
                |     ->route('peserta-faces.index')
                |
                */

                stopCamera()

                if (previewUrl.value) {
                    URL.revokeObjectURL(
                        previewUrl.value
                    )

                    previewUrl.value = null
                }

                capturedFile.value = null
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| BACK
|--------------------------------------------------------------------------
*/

const goBack = () => {
    stopCamera()

    if (previewUrl.value) {
        URL.revokeObjectURL(
            previewUrl.value
        )

        previewUrl.value = null
    }

    router.get(
        route('peserta-faces.index')
    )
}

/*
|--------------------------------------------------------------------------
| CLEANUP
|--------------------------------------------------------------------------
*/

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
                                       bg-indigo-100 text-indigo-600
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
                            class="text-2xl font-bold tracking-tight
                                   text-slate-900
                                   dark:text-white sm:text-3xl"
                        >
                            Edit Registrasi Wajah
                        </h1>

                        <p
                            class="mt-1 text-sm
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Perbarui data dan foto wajah peserta.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="goBack"
                        class="inline-flex items-center
                               justify-center gap-2 rounded-xl
                               border border-slate-200
                               bg-white px-4 py-2.5
                               text-sm font-semibold
                               text-slate-700 shadow-sm
                               transition hover:bg-slate-50
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
                    </button>
                </div>
            </div>

            <!-- MAIN -->
            <div class="grid gap-6 lg:grid-cols-5">

                <!-- LEFT -->
                <div class="lg:col-span-2">

                    <!-- DATA PESERTA -->
                    <div
                        class="rounded-2xl border
                               border-slate-200 bg-white p-5
                               shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <div class="mb-5">
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
                                Pilih peserta yang akan diperbarui.
                            </p>
                        </div>

                        <label
                            class="mb-2 block text-sm font-semibold
                                   text-slate-700
                                   dark:text-slate-200"
                        >
                            Peserta
                        </label>

                        <select
                            :value="selectedPeserta"
                            @change="selectPeserta"
                            class="w-full rounded-xl border
                                   border-slate-200 bg-slate-50
                                   px-4 py-3 text-sm
                                   text-slate-800 outline-none
                                   transition
                                   focus:border-indigo-500
                                   focus:ring-2
                                   focus:ring-indigo-500/20
                                   dark:border-slate-700
                                   dark:bg-slate-800
                                   dark:text-white"
                        >
                            <option value="">
                                -- Pilih Peserta --
                            </option>

                            <option
                                v-for="peserta in pesertas"
                                :key="peserta.id"
                                :value="peserta.id"
                            >
                                {{
                                    peserta.nama_lengkap ??
                                    peserta.nama ??
                                    'Peserta'
                                }}
                            </option>
                        </select>

                        <p
                            v-if="
                                localErrors.peserta_id ||
                                errors.peserta_id
                            "
                            class="mt-2 text-xs font-medium
                                   text-red-500"
                        >
                            {{
                                localErrors.peserta_id ??
                                errors.peserta_id
                            }}
                        </p>

                        <!-- PESERTA SAAT INI -->
                        <div
                            v-if="pesertaFace?.peserta"
                            class="mt-5 rounded-xl border
                                   border-indigo-100
                                   bg-indigo-50 p-4
                                   dark:border-indigo-500/20
                                   dark:bg-indigo-500/10"
                        >
                            <p
                                class="text-xs font-medium
                                       text-indigo-500"
                            >
                                Peserta saat ini
                            </p>

                            <p
                                class="mt-1 font-bold
                                       text-slate-900
                                       dark:text-white"
                            >
                                {{
                                    pesertaFace.peserta.nama_lengkap ??
                                    pesertaFace.peserta.nama ??
                                    '-'
                                }}
                            </p>

                            <p
                                v-if="
                                    pesertaFace.peserta.no_induk
                                "
                                class="mt-1 text-xs
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                No. Induk:
                                {{ pesertaFace.peserta.no_induk }}
                            </p>
                        </div>
                    </div>

                    <!-- FOTO LAMA -->
                    <div
                        class="mt-6 rounded-2xl border
                               border-slate-200 bg-white p-5
                               shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <h2
                            class="text-base font-bold
                                   text-slate-900
                                   dark:text-white"
                        >
                            Foto Terdaftar
                        </h2>

                        <p
                            class="mt-1 text-xs
                                   text-slate-500
                                   dark:text-slate-400"
                        >
                            Foto wajah yang saat ini tersimpan.
                        </p>

                        <div
                            class="mt-4 overflow-hidden rounded-xl
                                   border border-slate-200
                                   bg-slate-100
                                   dark:border-slate-700
                                   dark:bg-slate-800"
                        >
                            <img
                                v-if="oldPhotoUrl()"
                                :src="oldPhotoUrl()"
                                alt="Foto wajah lama"
                                class="aspect-square w-full
                                       object-cover"
                            >

                            <div
                                v-else
                                class="flex aspect-square
                                       items-center justify-center"
                            >
                                <div class="text-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mx-auto h-12 w-12
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
                                        class="mt-2 text-sm
                                               text-slate-500
                                               dark:text-slate-400"
                                    >
                                        Belum ada foto
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="lg:col-span-3">

                    <div
                        class="rounded-2xl border
                               border-slate-200 bg-white p-5
                               shadow-sm
                               dark:border-slate-800
                               dark:bg-slate-900"
                    >
                        <!-- HEADER CAMERA -->
                        <div
                            class="mb-5 flex items-center
                                   justify-between gap-4"
                        >
                            <div>
                                <h2
                                    class="text-base font-bold
                                           text-slate-900
                                           dark:text-white"
                                >
                                    Perbarui Foto Wajah
                                </h2>

                                <p
                                    class="mt-1 text-xs
                                           text-slate-500
                                           dark:text-slate-400"
                                >
                                    Foto baru bersifat opsional.
                                </p>
                            </div>

                            <div
                                class="rounded-full px-3 py-1
                                       text-xs font-semibold"
                                :class="
                                    cameraReady
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                        : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                "
                            >
                                {{
                                    cameraReady
                                        ? 'Kamera aktif'
                                        : 'Kamera tidak aktif'
                                }}
                            </div>
                        </div>

                        <!-- CAMERA -->
                        <div
                            class="relative overflow-hidden
                                   rounded-2xl bg-slate-950"
                        >
                            <video
                                v-if="!previewUrl"
                                ref="video"
                                autoplay
                                playsinline
                                muted
                                class="aspect-video w-full
                                       object-cover"
                                style="
                                    transform: scaleX(-1);
                                "
                            ></video>

                            <img
                                v-else
                                :src="previewUrl"
                                alt="Preview foto baru"
                                class="aspect-video w-full
                                       object-cover"
                            />

                            <!-- FACE GUIDE -->
                            <div
                                v-if="!previewUrl"
                                class="pointer-events-none
                                       absolute inset-0 flex
                                       items-center justify-center"
                            >
                                <div
                                    class="h-64 w-48 rounded-[50%]
                                           border-2 border-dashed
                                           border-white/70
                                           sm:h-72 sm:w-56"
                                ></div>
                            </div>

                            <!-- CAMERA LOADING -->
                            <div
                                v-if="cameraLoading"
                                class="absolute inset-0 flex
                                       items-center justify-center
                                       bg-black/50"
                            >
                                <div
                                    class="text-center text-white"
                                >
                                    <svg
                                        class="mx-auto h-8 w-8
                                               animate-spin"
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

                                    <p class="mt-2 text-sm">
                                        Membuka kamera...
                                    </p>
                                </div>
                            </div>

                            <!-- CAMERA ERROR -->
                            <div
                                v-if="cameraError"
                                class="absolute inset-x-4
                                       bottom-4 rounded-xl
                                       bg-red-500/90 px-4 py-3
                                       text-center text-xs
                                       font-medium text-white"
                            >
                                {{ cameraError }}
                            </div>
                        </div>

                        <!-- ACTION CAMERA -->
                        <div
                            class="mt-4 flex flex-col gap-3
                                   sm:flex-row"
                        >
                            <!-- OPEN CAMERA -->
                            <button
                                v-if="
                                    !cameraReady &&
                                    !previewUrl
                                "
                                type="button"
                                @click="startCamera"
                                :disabled="cameraLoading"
                                class="inline-flex flex-1
                                       items-center justify-center
                                       gap-2 rounded-xl
                                       bg-indigo-600 px-5 py-3
                                       text-sm font-bold text-white
                                       shadow-sm transition
                                       hover:bg-indigo-700
                                       disabled:cursor-not-allowed
                                       disabled:opacity-50"
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
                                        d="M3 8l2-2h4l2-2h2l2 2h4l2 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="13"
                                        r="3"
                                    />
                                </svg>

                                Buka Kamera
                            </button>

                            <!-- CAPTURE -->
                            <button
                                v-if="cameraReady"
                                type="button"
                                @click="capturePhoto"
                                class="inline-flex flex-1
                                       items-center justify-center
                                       gap-2 rounded-xl
                                       bg-emerald-600 px-5 py-3
                                       text-sm font-bold text-white
                                       shadow-sm transition
                                       hover:bg-emerald-700"
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
                                        d="M3 8l2-2h4l2-2h2l2 2h4l2 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="13"
                                        r="3"
                                    />
                                </svg>

                                Ambil Foto
                            </button>

                            <!-- RETAKE -->
                            <button
                                v-if="previewUrl"
                                type="button"
                                @click="retakePhoto"
                                class="inline-flex flex-1
                                       items-center justify-center
                                       gap-2 rounded-xl
                                       border border-slate-200
                                       bg-white px-5 py-3
                                       text-sm font-bold
                                       text-slate-700
                                       transition hover:bg-slate-50
                                       dark:border-slate-700
                                       dark:bg-slate-800
                                       dark:text-slate-200
                                       dark:hover:bg-slate-700"
                            >
                                Ambil Ulang
                            </button>
                        </div>

                        <!-- INFO -->
                        <div
                            class="mt-5 rounded-xl border
                                   border-amber-200
                                   bg-amber-50 p-4
                                   dark:border-amber-500/20
                                   dark:bg-amber-500/10"
                        >
                            <div class="flex gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mt-0.5 h-5 w-5 shrink-0
                                           text-amber-500"
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
                                               text-amber-800
                                               dark:text-amber-300"
                                    >
                                        Catatan
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5
                                               text-amber-700
                                               dark:text-amber-400"
                                    >
                                        Jika tidak mengambil foto baru,
                                        foto lama dan data embedding
                                        tetap digunakan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ERROR PHOTO -->
                        <p
                            v-if="
                                localErrors.photo ||
                                errors.photo
                            "
                            class="mt-3 text-xs font-medium
                                   text-red-500"
                        >
                            {{
                                localErrors.photo ??
                                errors.photo
                            }}
                        </p>

                        <!-- ERROR GENERAL -->
                        <p
                            v-if="
                                localErrors.face_embedding ||
                                errors.face_embedding
                            "
                            class="mt-3 text-xs font-medium
                                   text-red-500"
                        >
                            {{
                                localErrors.face_embedding ??
                                errors.face_embedding
                            }}
                        </p>

                        <!-- SUBMIT -->
                        <div
                            class="mt-6 flex flex-col-reverse
                                   gap-3 border-t
                                   border-slate-200 pt-5
                                   dark:border-slate-800
                                   sm:flex-row
                                   sm:justify-end"
                        >
                            <!-- BATAL -->
                            <button
                                type="button"
                                @click="goBack"
                                :disabled="processing"
                                class="rounded-xl border
                                       border-slate-200
                                       bg-white px-5 py-3
                                       text-sm font-semibold
                                       text-slate-700 transition
                                       hover:bg-slate-50
                                       disabled:cursor-not-allowed
                                       disabled:opacity-50
                                       dark:border-slate-700
                                       dark:bg-slate-800
                                       dark:text-slate-200
                                       dark:hover:bg-slate-700"
                            >
                                Batal
                            </button>

                            <!-- SIMPAN -->
                            <button
                                type="button"
                                @click="submit"
                                :disabled="processing"
                                class="inline-flex
                                       items-center justify-center
                                       gap-2 rounded-xl
                                       bg-indigo-600 px-6 py-3
                                       text-sm font-bold text-white
                                       shadow-sm transition
                                       hover:bg-indigo-700
                                       disabled:cursor-not-allowed
                                       disabled:opacity-50"
                            >
                                <svg
                                    v-if="processing"
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

                                {{
                                    processing
                                        ? 'Memproses Wajah...'
                                        : 'Simpan Perubahan'
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HIDDEN CANVAS -->
            <canvas
                ref="canvas"
                class="hidden"
            ></canvas>
        </div>
    </div>
</template>

