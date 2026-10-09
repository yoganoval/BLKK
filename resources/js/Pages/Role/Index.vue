<script setup>
import { ref, watch, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AdminLayout.vue'
import { useDebounce } from '@/composables/useDebounce'

const props = defineProps({
    roles: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

// ============================================================
// INISIALISASI FILTER DARI PROPS
// ============================================================

const search = ref(props.filters?.search ?? '')
const perPage = ref(props.filters?.perPage ?? 10)

// ============================================================
// DEBOUNCE SEARCH
// ============================================================

const debouncedSearch = useDebounce(search, 500)

// Watch debounced search
watch(debouncedSearch, (newSearch) => {
    router.get(
        route('roles.index'),
        {
            search: newSearch,
            perPage: perPage.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    )
})

// ============================================================
// WATCH PERPAGE
// ============================================================

watch(perPage, (newPerPage) => {
    router.get(
        route('roles.index'),
        {
            search: search.value,
            perPage: newPerPage
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    )
})

// ============================================================
// FORMAT TANGGAL
// ============================================================

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(tanggal)

    if (isNaN(date.getTime())) return '-'

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

// ============================================================
// CREATE
// ============================================================

const createRole = () => {
    router.visit(route('roles.create'))
}

// ============================================================
// EDIT
// ============================================================

const editRole = (role) => {
    router.visit(route('roles.edit', role.id))
}

// ============================================================
// DELETE
// ============================================================

const deleteRole = (role) => {
    if (!confirm(`Yakin ingin menghapus role "${role.name}"?`)) {
        return
    }

    router.delete(route('roles.destroy', role.id), {
        preserveScroll: true,
    })
}

// ============================================================
// PAGINATION
// ============================================================

const goToPage = (url) => {
    if (!url) return

    router.get(
        url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
}
</script>

<template>
    <Head title="Role" />

    <AuthenticatedLayout>

        <!-- =====================================================
            HEADER
        ====================================================== -->

        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            Manajemen Role
                        </h1>
                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                            Kelola role dan hak akses pengguna sistem
                        </p>
                    </div>
                </div>
            </div>

            <Link
                :href="route('roles.create')"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 transition-all duration-200 hover:scale-105 hover:shadow-emerald-500/40 dark:shadow-emerald-500/15"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Role
            </Link>
        </div>

        <!-- =====================================================
            CONTENT
        ====================================================== -->

        <div class="py-6">
            <div class="mx-auto max-w-10xl">

                <!-- =================================================
                    MAIN CARD
                ================================================== -->

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">

                    <!-- =================================================
                        FILTER / TOOLBAR
                    ================================================== -->

                    <div class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">

                        <!-- SEARCH -->
                        <div class="relative w-full sm:max-w-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari role..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-10 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:bg-slate-800"
                            />

                            <!-- CLEAR BUTTON -->
                            <button
                                v-if="search.length > 0"
                                type="button"
                                @click="search = ''"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- PER PAGE -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-slate-500 dark:text-slate-400">
                                Tampilkan
                            </span>

                            <select
                                v-model="perPage"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            >
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>

                            <span class="text-sm text-slate-400 dark:text-slate-500">
                                per halaman
                            </span>
                        </div>

                    </div>

                    <!-- =================================================
                        TABLE
                    ================================================== -->

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">

                            <thead class="bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500 backdrop-blur-sm dark:bg-slate-800/50 dark:text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Nama Role</th>
                                    <th class="px-6 py-4">Guard</th>
                                    <th class="px-6 py-4">Dibuat</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                                <!-- DATA ROWS -->
                                <tr
                                    v-for="(role, index) in roles.data"
                                    :key="role.id"
                                    class="group transition hover:bg-slate-50/60 dark:hover:bg-slate-800/30"
                                >
                                    <!-- NO -->
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-400 dark:text-slate-500">
                                        {{ (roles.current_page - 1) * roles.per_page + index + 1 }}
                                    </td>

                                    <!-- NAMA ROLE -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <!-- ICON -->
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-md shadow-blue-500/20 dark:shadow-blue-500/10">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-3v6m3-3h-6" />
                                                </svg>
                                            </div>

                                            <!-- NAME -->
                                            <div>
                                                <p class="font-semibold text-slate-800 dark:text-white">
                                                    {{ role.name }}
                                                </p>
                                                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                                                    ID: #{{ role.id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- GUARD -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                            {{ role.guard_name }}
                                        </span>
                                    </td>

                                    <!-- CREATED -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ formatTanggal(role.created_at) }}</span>
                                        </div>
                                    </td>

                                    <!-- ACTION -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex justify-end gap-1.5">
                                            <!-- EDIT BUTTON -->
                                            <button
                                                type="button"
                                                @click="editRole(role)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500/10 px-3 py-2 text-xs font-medium text-yellow-600 transition hover:bg-yellow-500 hover:text-white dark:bg-yellow-500/20 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.651 3.651M5 20h4l10.5-10.5a2.586 2.586 0 0 0-3.66-3.66L5.34 16.34 5 20Z" />
                                                </svg>
                                                Edit
                                            </button>

                                            <!-- DELETE BUTTON -->
                                            <button
                                                type="button"
                                                @click="deleteRole(role)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-500/10 px-3 py-2 text-xs font-medium text-red-600 transition hover:bg-red-500 hover:text-white dark:bg-red-500/20 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 4v6m6-6v6M9 7V4h6v3m-8 0 1 13h8l1-13" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- EMPTY STATE -->
                                <tr v-if="!roles.data || roles.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2m16 0-3 3H7l-3-3" />
                                                </svg>
                                            </div>
                                            <p class="text-base font-semibold text-slate-700 dark:text-slate-300">
                                                Data role tidak ditemukan
                                            </p>
                                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                Belum ada data role yang tersedia. Tambahkan role baru sekarang.
                                            </p>
                                            <Link
                                                :href="route('roles.create')"
                                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Tambah Role
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </div>

                    <!-- =================================================
                        FOOTER / PAGINATION
                    ================================================== -->

                    <div v-if="roles.data && roles.data.length > 0" class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">

                        <!-- INFO -->
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Menampilkan
                            <span class="font-semibold text-slate-700 dark:text-white">
                                {{ roles.from ?? 0 }}
                            </span>
                            -
                            <span class="font-semibold text-slate-700 dark:text-white">
                                {{ roles.to ?? 0 }}
                            </span>
                            dari
                            <span class="font-semibold text-slate-700 dark:text-white">
                                {{ roles.total ?? 0 }}
                            </span>
                            role
                        </div>

                        <!-- PAGINATION LINKS -->
                        <div v-if="roles.links?.length > 3" class="flex flex-wrap gap-1">
                            <template v-for="link in roles.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    preserve-state
                                    class="min-w-9 rounded-lg px-3 py-2 text-sm font-medium transition"
                                    :class="
                                        link.active
                                            ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20 dark:shadow-emerald-500/10'
                                            : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                    "
                                >
                                    <span v-html="link.label" />
                                </Link>

                                <span
                                    v-else
                                    class="min-w-9 rounded-lg px-3 py-2 text-center text-sm text-slate-300 dark:text-slate-600"
                                >
                                    <span v-html="link.label" />
                                </span>
                            </template>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>