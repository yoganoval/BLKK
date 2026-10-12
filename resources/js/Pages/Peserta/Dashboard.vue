<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const user = page.props.auth?.user as
    | { nama?: string; name?: string; username?: string }
    | undefined

const namaPeserta =
    user?.nama ?? user?.name ?? user?.username ?? 'Peserta'
</script>

<template>
    <Head title="Dashboard Peserta" />

    <div class="min-h-screen bg-gray-50 text-gray-800">
        <header
            class="flex items-center justify-between border-b bg-white px-5 py-4 md:px-8"
        >
            <div>
                <h1 class="text-lg font-bold text-blue-700">
                    BLKK Salafiyah Syafi’iyah
                </h1>
                <p class="text-sm text-gray-500">Portal Peserta Pelatihan</p>
            </div>

            <Link
                :href="route('peserta.logout')"
                method="post"
                as="button"
                class="rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100"
            >
                Keluar
            </Link>
        </header>

        <main class="mx-auto max-w-7xl space-y-6 px-5 py-8 md:px-8">
            <section
                class="rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 p-6 text-white shadow-sm md:p-8"
            >
                <p class="text-sm text-blue-100">Selamat datang,</p>
                <h2 class="mt-2 text-2xl font-bold md:text-3xl">
                    {{ namaPeserta }}!
                </h2>
                <p class="mt-2 text-sm text-blue-100">
                    Pantau kegiatan pelatihanmu melalui portal peserta BLKK.
                </p>
            </section>

            <section>
                <h2 class="mb-4 text-lg font-bold">Menu Peserta</h2>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        href="/peserta/dashboard"
                        class="rounded-xl border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-xl"
                        >
                            📚
                        </div>
                        <h3 class="font-semibold">Pelatihan Saya</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Lihat informasi pelatihan yang diikuti.
                        </p>
                    </Link>

                    <div class="rounded-xl border bg-white p-5 shadow-sm">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-xl"
                        >
                            📝
                        </div>
                        <h3 class="font-semibold">Tugas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Akses tugas dari instruktur.
                        </p>
                    </div>

                    <div class="rounded-xl border bg-white p-5 shadow-sm">
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-xl"
                        >
                            📊
                        </div>
                        <h3 class="font-semibold">Nilai</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Pantau hasil penilaian pelatihan.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
