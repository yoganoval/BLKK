<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import login from '@/routes/login'

const showPassword = ref(false)

const form = useForm({
    username: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(login.store().url, {
        onFinish: () => {
            form.reset('password')
        },
    })
}
</script>

<template>
    <Head title="Login BLK Multimedia" />

    <div class="min-h-screen bg-white lg:grid lg:grid-cols-2">

        <!-- ================================================= -->
        <!-- LEFT : FOTO BLK MULTIMEDIA -->
        <!-- ================================================= -->
        <div class="relative hidden overflow-hidden lg:block">

            <!-- Foto BLK Multimedia -->
            <img
                src="/storage/images/blk-multimedia.jpg"
                alt="BLK Komunitas Multimedia"
                class="absolute inset-0 h-full w-full object-cover"
            />

            <!-- Overlay Gradient -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-indigo-950/90 via-purple-900/70 to-blue-700/40"
            ></div>

            <!-- Decorative Elements -->
            <div
                class="absolute -left-32 -top-32 h-80 w-80 rounded-full border border-white/10"
            ></div>
            <div
                class="absolute -bottom-40 -right-40 h-[500px] w-[500px] rounded-full border border-white/10"
            ></div>
            <div
                class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/5"
            ></div>

            <!-- Floating Icons -->
            <div class="absolute right-12 top-32 animate-pulse opacity-30">
                <svg class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="absolute bottom-32 left-12 animate-pulse opacity-20" style="animation-delay: 2s;">
                <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>

            <!-- Content -->
            <div
                class="relative z-10 flex h-full flex-col justify-between p-12 xl:p-16"
            >

                <!-- Logo -->
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-white shadow-lg shadow-indigo-500/30"
                    >
                        <img
                            src="/storage/images/logo.png"
                            alt="Logo BLK Multimedia"
                            class="h-full w-full object-contain p-1"
                        />
                    </div>

                    <div>
                        <h1 class="text-xl font-bold tracking-wide text-white">
                            BLK Multimedia
                        </h1>
                        <p class="text-xs text-indigo-200">
                            Balai Latihan Kerja Komunitas
                        </p>
                    </div>
                </div>

                <!-- Main Text -->
                <div class="max-w-xl">

                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-sm"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-medium text-white">
                            Sistem Manajemen Pelatihan
                        </span>
                    </div>

                    <h2
                        class="text-4xl font-bold leading-tight text-white xl:text-5xl"
                    >
                        Tingkatkan Kompetensi
                        <span class="text-indigo-200">
                            Digital
                        </span>
                        dengan Pelatihan Berkualitas.
                    </h2>

                    <p
                        class="mt-6 max-w-lg text-sm leading-7 text-indigo-100 xl:text-base"
                    >
                        BLK Komunitas Multimedia menyediakan pelatihan berbasis kompetensi
                        di bidang multimedia, desain grafis, animasi, dan produksi konten
                        digital untuk meningkatkan keterampilan dan daya saing tenaga kerja.
                    </p>

                    <!-- Feature Grid -->
                    <div class="mt-8 grid max-w-lg grid-cols-2 gap-4">

                        <div
                            class="rounded-xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"
                                    />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-white">
                                Pendaftaran
                            </p>
                            <p class="mt-1 text-xs text-indigo-100">
                                Kelola peserta pelatihan
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-white">
                                Sertifikasi
                            </p>
                            <p class="mt-1 text-xs text-indigo-100">
                                Uji kompetensi & sertifikat
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-white">
                                Laporan
                            </p>
                            <p class="mt-1 text-xs text-indigo-100">
                                Monitoring & evaluasi
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm transition hover:bg-white/20"
                        >
                            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-pink-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                                    />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-white">
                                Pembinaan
                            </p>
                            <p class="mt-1 text-xs text-indigo-100">
                                Bimbingan peserta
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between text-xs text-indigo-100">
                    <span>© {{ new Date().getFullYear() }} BLK Multimedia</span>
                    <span class="flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Sistem Aktif
                    </span>
                </div>

            </div>
        </div>


        <!-- ================================================= -->
        <!-- RIGHT : LOGIN -->
        <!-- ================================================= -->
        <div
            class="flex min-h-screen items-center justify-center bg-gradient-to-b from-slate-50 to-slate-100/50 px-6 py-12 sm:px-10 lg:px-16 xl:px-24"
        >

            <div class="w-full max-w-md">

                <!-- Mobile Logo -->
                <div class="mb-10 lg:hidden">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-600/20"
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
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>

                        <div>
                            <h1 class="font-bold text-slate-900">
                                BLK Multimedia
                            </h1>
                            <p class="text-xs text-slate-500">
                                Balai Latihan Kerja Komunitas
                            </p>
                        </div>

                    </div>

                </div>


                <!-- Heading -->
                <div class="mb-8">

                    <p class="mb-2 text-sm font-medium text-indigo-600">
                        Selamat Datang 👋
                    </p>

                    <h2 class="text-3xl font-bold tracking-tight text-slate-900">
                        Masuk ke Akun Anda
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Silakan masukkan username dan password untuk mengakses
                        sistem manajemen BLK Multimedia.
                    </p>

                </div>


                <!-- Form -->
                <form
                    @submit.prevent="submit"
                    class="space-y-5"
                >

                    <!-- Username -->
                    <div>

                        <label
                            for="username"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Username
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4.5 20.25a7.5 7.5 0 0115 0"
                                    />
                                </svg>
                            </div>

                            <input
                                id="username"
                                v-model="form.username"
                                type="text"
                                autocomplete="username"
                                autofocus
                                placeholder="Masukkan username"
                                class="h-12 w-full rounded-xl border bg-white pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                                :class="
                                    form.errors.username
                                        ? 'border-red-400'
                                        : 'border-slate-200'
                                "
                            />

                        </div>

                        <p
                            v-if="form.errors.username"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.username }}
                        </p>

                    </div>


                    <!-- Password -->
                    <div>

                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Password
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <rect
                                        width="16"
                                        height="11"
                                        x="4"
                                        y="10"
                                        rx="2"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 10V7a4 4 0 018 0v3"
                                    />
                                </svg>
                            </div>

                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="h-12 w-full rounded-xl border bg-white pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                                :class="
                                    form.errors.password
                                        ? 'border-red-400'
                                        : 'border-slate-200'
                                "
                            />

                            <!-- Show Password -->
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-slate-600"
                            >
                                <svg
                                    v-if="!showPassword"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12 18.25 18.75 12 18.75 2.25 12 2.25 12z"
                                    />
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                    />
                                </svg>

                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 3l18 18"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M10.58 10.58a2 2 0 002.84 2.84"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9.88 5.1A10.7 10.7 0 0112 4.9c6.25 0 9.75 7.1 9.75 7.1a18.7 18.7 0 01-3.17 4.18"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6.61 6.62C3.83 8.52 2.25 12 2.25 12a18.7 18.7 0 004.05 5.03A10.73 10.73 0 0012 19.1"
                                    />
                                </svg>
                            </button>

                        </div>

                        <p
                            v-if="form.errors.password"
                            class="mt-2 text-xs font-medium text-red-500"
                        >
                            {{ form.errors.password }}
                        </p>

                    </div>


                    <!-- Remember -->
                    <div class="flex items-center justify-between">

                        <label
                            class="flex cursor-pointer items-center gap-2"
                        >

                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            />

                            <span class="text-sm text-slate-600">
                                Ingat saya
                            </span>

                        </label>

                    </div>


                    <!-- General Error -->
                    <div
                        v-if="
                            form.hasErrors &&
                            !form.errors.username &&
                            !form.errors.password
                        "
                        class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600"
                    >
                        Username atau password yang Anda masukkan salah.
                    </div>


                    <!-- Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:shadow-indigo-600/30 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <svg
                            v-if="form.processing"
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
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        <span>
                            {{ form.processing ? 'Memproses...' : 'Masuk ke BLK Multimedia' }}
                        </span>

                        <svg
                            v-if="!form.processing"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12h14M13 6l6 6-6 6"
                            />
                        </svg>

                    </button>

                </form>


                <!-- Bottom -->
                <div class="mt-8 text-center">

                    <p class="text-xs text-slate-400">
                        Akses sistem hanya untuk pengguna yang memiliki akun terdaftar
                        di BLK Komunitas Multimedia.
                    </p>

                    <div class="mt-4 flex items-center justify-center gap-4 text-xs text-slate-400">
                        <span>🔒 SSL Secure</span>
                        <span>•</span>
                        <span>📋 Verifikasi 2FA</span>
                    </div>

                </div>

            </div>

        </div>

    </div>
</template>