<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const video = ref(null)
const canvas = ref(null)
const stream = ref(null)

const cameraActive = ref(false)
const capturedPhoto = ref(null)
const submitting = ref(false)

const form = useForm({
    photo: null,
})

/*
|--------------------------------------------------------------------------
| Kamera
|--------------------------------------------------------------------------
*/

const startCamera = async () => {
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
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
            video.value.srcObject = stream.value
            await video.value.play()
            cameraActive.value = true
        }
    } catch (error) {
        console.error('Camera error:', error)

        alert(
            'Kamera tidak dapat diakses. Pastikan browser memiliki izin menggunakan kamera.'
        )
    }
}

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach((track) => {
            track.stop()
        })

        stream.value = null
    }

    cameraActive.value = false
}

/*
|--------------------------------------------------------------------------
| Ambil Foto
|--------------------------------------------------------------------------
*/

const capturePhoto = () => {
    if (!video.value || !canvas.value) {
        return
    }

    const width = video.value.videoWidth
    const height = video.value.videoHeight

    if (!width || !height) {
        alert('Kamera belum siap.')
        return
    }

    canvas.value.width = width
    canvas.value.height = height

    const context = canvas.value.getContext('2d')

    /*
     * Mirror mengikuti tampilan kamera.
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

    capturedPhoto.value = canvas.value.toDataURL(
        'image/jpeg',
        0.9
    )

    /*
     * Convert base64 → File
     */
    canvas.value.toBlob(
        (blob) => {
            if (!blob) {
                return
            }

            form.photo = new File(
                [blob],
                `face-${props.user.id}-${Date.now()}.jpg`,
                {
                    type: 'image/jpeg',
                }
            )
        },
        'image/jpeg',
        0.9
    )

    stopCamera()
}

/*
|--------------------------------------------------------------------------
| Ambil Ulang
|--------------------------------------------------------------------------
*/

const retakePhoto = async () => {
    capturedPhoto.value = null
    form.photo = null

    await startCamera()
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {
    if (!form.photo) {
        alert('Silakan ambil foto wajah terlebih dahulu.')
        return
    }

    submitting.value = true

    form.post(
        route('users.face.store', props.user.id),
        {
            forceFormData: true,

            preserveScroll: true,

            onFinish: () => {
                submitting.value = false
            },

            onSuccess: () => {
                capturedPhoto.value = null
                form.photo = null
            },
        }
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
})
</script>

<template>

    <Head :title="`Daftar Wajah - ${user.name}`" />

    <div class="space-y-6">

        <!-- HEADER -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-800 dark:text-white"
                >
                    Daftarkan Wajah
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Daftarkan wajah staff untuk digunakan pada absensi mobile.
                </p>
            </div>

            <Link
                :href="route('users.index')"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
            >
                ← Kembali
            </Link>
        </div>


        <!-- USER INFO -->
        <div
            class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >
            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400"
                >
                    {{ user.name?.charAt(0)?.toUpperCase() }}
                </div>

                <div class="min-w-0">
                    <h2
                        class="truncate font-semibold text-slate-800 dark:text-white"
                    >
                        {{ user.name }}
                    </h2>

                    <p
                        class="text-sm text-slate-500 dark:text-slate-400"
                    >
                        @{{ user.username }}
                    </p>
                </div>

                <div class="ml-auto">

                    <span
                        v-if="user.face_registered"
                        class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400"
                    >
                        ✓ Wajah Terdaftar
                    </span>

                    <span
                        v-else
                        class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400"
                    >
                        Belum Terdaftar
                    </span>

                </div>

            </div>
        </div>


        <!-- MAIN -->
        <div class="grid gap-6 lg:grid-cols-2">

            <!-- CAMERA -->
            <div
                class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800"
            >

                <div
                    class="border-b border-slate-200 px-5 py-4 dark:border-slate-700"
                >
                    <h2
                        class="font-semibold text-slate-800 dark:text-white"
                    >
                        Kamera
                    </h2>

                    <p
                        class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                    >
                        Pastikan wajah terlihat jelas dan berada di tengah.
                    </p>
                </div>


                <div class="p-5">

                    <!-- CAMERA AREA -->
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl bg-slate-950"
                    >

                        <video
                            ref="video"
                            v-show="cameraActive && !capturedPhoto"
                            autoplay
                            playsinline
                            muted
                            class="h-full w-full object-cover"
                            style="transform: scaleX(-1)"
                        ></video>


                        <!-- PHOTO RESULT -->
                        <img
                            v-if="capturedPhoto"
                            :src="capturedPhoto"
                            alt="Preview wajah"
                            class="h-full w-full object-cover"
                        />


                        <!-- CAMERA LOADING -->
                        <div
                            v-if="!cameraActive && !capturedPhoto"
                            class="absolute inset-0 flex flex-col items-center justify-center px-6 text-center text-white"
                        >
                            <div
                                class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-white/10"
                            >
                                📷
                            </div>

                            <p class="text-sm font-medium">
                                Kamera belum aktif
                            </p>

                            <button
                                type="button"
                                @click="startCamera"
                                class="mt-4 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-100"
                            >
                                Aktifkan Kamera
                            </button>
                        </div>


                        <!-- FACE GUIDE -->
                        <div
                            v-if="cameraActive && !capturedPhoto"
                            class="pointer-events-none absolute inset-0 flex items-center justify-center"
                        >
                            <div
                                class="h-64 w-48 rounded-[50%] border-2 border-dashed border-white/80"
                            ></div>
                        </div>

                    </div>


                    <!-- HIDDEN CANVAS -->
                    <canvas
                        ref="canvas"
                        class="hidden"
                    ></canvas>


                    <!-- ACTION -->
                    <div class="mt-5 flex gap-3">

                        <button
                            v-if="cameraActive && !capturedPhoto"
                            type="button"
                            @click="capturePhoto"
                            class="flex-1 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            📷 Ambil Foto
                        </button>


                        <button
                            v-if="capturedPhoto"
                            type="button"
                            @click="retakePhoto"
                            class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
                        >
                            ↻ Ambil Ulang
                        </button>

                    </div>

                </div>
            </div>


            <!-- INFO -->
            <div class="space-y-6">

                <!-- PETUNJUK -->
                <div
                    class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
                >

                    <h2
                        class="font-semibold text-slate-800 dark:text-white"
                    >
                        Petunjuk Pendaftaran
                    </h2>

                    <div class="mt-4 space-y-4">

                        <div class="flex gap-3">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400"
                            >
                                1
                            </div>

                            <p
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Posisikan wajah tepat di tengah kamera.
                            </p>
                        </div>


                        <div class="flex gap-3">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400"
                            >
                                2
                            </div>

                            <p
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Pastikan pencahayaan cukup dan wajah tidak tertutup.
                            </p>
                        </div>


                        <div class="flex gap-3">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400"
                            >
                                3
                            </div>

                            <p
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Ambil satu foto wajah dengan posisi menghadap kamera.
                            </p>
                        </div>


                        <div class="flex gap-3">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400"
                            >
                                4
                            </div>

                            <p
                                class="text-sm text-slate-600 dark:text-slate-300"
                            >
                                Sistem akan memproses wajah menggunakan FaceNet512.
                            </p>
                        </div>

                    </div>
                </div>


                <!-- STATUS -->
                <div
                    class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800"
                >

                    <h2
                        class="font-semibold text-slate-800 dark:text-white"
                    >
                        Status Wajah
                    </h2>

                    <div class="mt-4">

                        <div
                            v-if="user.face_registered"
                            class="rounded-lg bg-emerald-50 p-4 dark:bg-emerald-900/20"
                        >

                            <p
                                class="text-sm font-semibold text-emerald-700 dark:text-emerald-400"
                            >
                                ✓ Wajah sudah terdaftar
                            </p>

                            <p
                                v-if="user.face_registered_at"
                                class="mt-1 text-xs text-emerald-600 dark:text-emerald-500"
                            >
                                Terdaftar:
                                {{ user.face_registered_at }}
                            </p>

                        </div>

                        <div
                            v-else
                            class="rounded-lg bg-amber-50 p-4 dark:bg-amber-900/20"
                        >

                            <p
                                class="text-sm font-semibold text-amber-700 dark:text-amber-400"
                            >
                                Wajah belum terdaftar
                            </p>

                            <p
                                class="mt-1 text-xs text-amber-600 dark:text-amber-500"
                            >
                                Silakan ambil foto wajah untuk mendaftarkan
                                staff ini.
                            </p>

                        </div>

                    </div>
                </div>


                <!-- SUBMIT -->
                <div
                    class="rounded-xl border border-indigo-100 bg-indigo-50 p-5 dark:border-indigo-900/50 dark:bg-indigo-900/20"
                >

                    <p
                        class="text-sm text-indigo-700 dark:text-indigo-300"
                    >
                        Setelah foto diambil, tekan tombol berikut untuk
                        memproses dan menyimpan data wajah.
                    </p>

                    <button
                        type="button"
                        :disabled="!form.photo || submitting"
                        @click="submit"
                        class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="submitting">
                            Memproses Wajah...
                        </span>

                        <span v-else>
                            ✓ Daftarkan Wajah
                        </span>
                    </button>

                    <!-- ERROR -->
                    <p
                        v-if="form.errors.photo"
                        class="mt-3 text-sm font-medium text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.photo }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</template>