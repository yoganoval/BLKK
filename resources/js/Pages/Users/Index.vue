<script setup>
import { computed, ref, watch } from 'vue'
import { Head, router, useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
    layout: AdminLayout,
})

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },

    roles: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            per_page: 10,
        }),
    },
})

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const search = ref(props.filters?.search ?? '')
const perPage = ref(Number(props.filters?.per_page ?? 10))

const showModal = ref(false)
const editMode = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const selectedUser = ref(null)

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
    username: '',
    name: '',
    password: '',
    password_confirmation: '',
    role: '',
})

/*
|--------------------------------------------------------------------------
| Modal Title
|--------------------------------------------------------------------------
*/

const modalTitle = computed(() => {
    return editMode.value ? 'Edit User' : 'Tambah User'
})

/*
|--------------------------------------------------------------------------
| Open Add Modal
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {
    editMode.value = false
    selectedUser.value = null

    form.reset()
    form.clearErrors()

    showPassword.value = false
    showConfirmPassword.value = false

    showModal.value = true
}

/*
|--------------------------------------------------------------------------
| Open Edit Modal
|--------------------------------------------------------------------------
*/

const openEditModal = (user) => {
    editMode.value = true
    selectedUser.value = user

    form.clearErrors()

    form.username = user.username ?? ''
    form.name = user.name ?? ''
    form.password = ''
    form.password_confirmation = ''

    form.role = user.roles?.length
        ? user.roles[0].id
        : ''

    showPassword.value = false
    showConfirmPassword.value = false

    showModal.value = true
}

/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {
    showModal.value = false

    form.reset()
    form.clearErrors()

    selectedUser.value = null
    editMode.value = false

    showPassword.value = false
    showConfirmPassword.value = false
}

/*
|--------------------------------------------------------------------------
| Submit Form
|--------------------------------------------------------------------------
*/

const submit = () => {
    if (editMode.value) {
        form.put(route('users.update', selectedUser.value.id), {
            preserveScroll: true,

            onSuccess: () => {
                closeModal()
            },
        })

        return
    }

    form.post(route('users.store'), {
        preserveScroll: true,

        onSuccess: () => {
            closeModal()
        },
    })
}

/*
|--------------------------------------------------------------------------
| Delete User
|--------------------------------------------------------------------------
*/

const deleteUser = (user) => {
    if (!confirm(`Yakin ingin menghapus user "${user.name}"?`)) {
        return
    }

    router.delete(route('users.destroy', user.id), {
        preserveScroll: true,
    })
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

let searchTimeout = null

watch(search, (value) => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(
            route('users.index'),
            {
                search: value,
                per_page: perPage.value,
            },
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            }
        )
    }, 400)
})

/*
|--------------------------------------------------------------------------
| Per Page
|--------------------------------------------------------------------------
*/

watch(perPage, (value) => {
    router.get(
        route('users.index'),
        {
            search: search.value,
            per_page: value,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
})

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const goToPage = (url) => {
    if (!url) {
        return
    }

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    })
}

/*
|--------------------------------------------------------------------------
| Pagination Label
|--------------------------------------------------------------------------
*/

const paginationLabel = (label) => {
    return label
        .replace('&laquo; Previous', '‹')
        .replace('Next &raquo;', '›')
}
</script>

<template>
    <Head title="User" />

    <div class="space-y-6">
        <!-- ===================================================== -->
        <!-- HEADER -->
        <!-- ===================================================== -->

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    User
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kelola data pengguna sistem.
                </p>
            </div>

            <button
                type="button"
                @click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Tambah User
            </button>
        </div>

        <!-- ===================================================== -->
        <!-- FILTER -->
        <!-- ===================================================== -->

        <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
            >
                <!-- Search -->

                <div class="relative w-full md:max-w-md">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                            />
                        </svg>
                    </div>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari username atau nama..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    />
                </div>

                <!-- Per Page -->

                <div class="flex items-center gap-2">
                    <span
                        class="text-sm text-slate-500 dark:text-slate-400"
                    >
                        Tampilkan
                    </span>

                    <select
                        v-model="perPage"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200"
                    >
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>

                    <span
                        class="text-sm text-slate-500 dark:text-slate-400"
                    >
                        data
                    </span>
                </div>
            </div>
        </div>

        <!-- ===================================================== -->
        <!-- TABLE -->
        <!-- ===================================================== -->

        <div
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-950/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                #
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Username
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Nama
                            </th>

                            <th
                                class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Role
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Foto Wajah
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-slate-200 dark:divide-slate-800"
                    >
                        <!-- Empty -->

                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center"
                            >
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mb-3 h-12 w-12 text-slate-300 dark:text-slate-700"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.125-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
                                        />
                                    </svg>

                                    <p
                                        class="text-sm font-medium text-slate-600 dark:text-slate-300"
                                    >
                                        Tidak ada data user
                                    </p>

                                    <p
                                        class="mt-1 text-sm text-slate-400"
                                    >
                                        Belum ada user yang tersedia.
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <!-- Data -->

                        <tr
                            v-for="(user, index) in users.data"
                            :key="user.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-slate-500 dark:text-slate-400"
                            >
                                {{ users.from + index }}
                            </td>

                            <td
                                class="whitespace-nowrap px-6 py-4"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400"
                                    >
                                        {{
                                            (user.name || user.username || 'U')
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </div>

                                    <span
                                        class="text-sm font-medium text-slate-900 dark:text-white"
                                    >
                                        {{ user.username }}
                                    </span>
                                </div>
                            </td>

                            <td
                                class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ user.name }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400"
                                    >
                                        {{ role.name }}
                                    </span>

                                    <span
                                        v-if="!user.roles?.length"
                                        class="text-xs text-slate-400"
                                    >
                                        Belum ada role
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-center">
                                    <img
                                        v-if="user.face_photo_path"
                                        :src="`/storage/${user.face_photo_path}`"
                                        alt="Foto Wajah"
                                        class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700"
                                    />

                                    <div
                                        v-else
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </td>

                            <td
                                class="whitespace-nowrap px-6 py-4"
                            >
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <!-- Edit -->

                                    <button
                                        type="button"
                                        @click="openEditModal(user)"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13l-2.685.895.895-2.685a4.5 4.5 0 0 1 1.13-1.897l8.837-9.026Z"
                                            />
                                        </svg>

                                        Edit
                                    </button>

                                    <!-- Wajah -->

                                    <Link
                                        :href="route('users.face.create', user.id)"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-900/50 dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:bg-indigo-900/40"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 19a4 4 0 0 0-6 0m9-7a6 6 0 1 1-12 0 6 6 0 0 1 12 0Zm-3-1a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                            />
                                        </svg>

                                        Wajah
                                    </Link>

                                    <!-- Delete -->

                                    <button
                                        type="button"
                                        @click="deleteUser(user)"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 7h12m-9 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m2 0v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7h12ZM10 11v6m4-6v6"
                                            />
                                        </svg>

                                        Hapus
                                    </button>
                                </div>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ================================================= -->
            <!-- PAGINATION -->
            <!-- ================================================= -->

            <div
                v-if="users.last_page > 1"
                class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
            >
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan
                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        {{ users.from }}
                    </span>
                    sampai
                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        {{ users.to }}
                    </span>
                    dari
                    <span class="font-medium text-slate-700 dark:text-slate-200">
                        {{ users.total }}
                    </span>
                    user
                </p>

                <div class="flex items-center gap-1">
                    <button
                        v-for="link in users.links"
                        :key="link.label"
                        type="button"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        class="min-w-9 rounded-lg px-3 py-2 text-sm transition"
                        :class="[
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : link.url
                                  ? 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                                  : 'cursor-not-allowed text-slate-300 dark:text-slate-700',
                        ]"
                        v-html="paginationLabel(link.label)"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL -->
    <!-- ========================================================= -->

    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <!-- Overlay -->

                <div
                    class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                    @click="closeModal"
                ></div>

                <!-- Modal -->

                <div
                    class="relative z-10 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
                >
                    <!-- Header -->

                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800"
                    >
                        <div>
                            <h2
                                class="text-lg font-bold text-slate-900 dark:text-white"
                            >
                                {{ modalTitle }}
                            </h2>

                            <p
                                class="mt-0.5 text-sm text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    editMode
                                        ? 'Perbarui informasi user.'
                                        : 'Tambahkan user baru.'
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18 18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->

                    <form
                        @submit.prevent="submit"
                        class="space-y-5 p-6"
                    >
                        <!-- Username -->

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Username
                            </label>

                            <input
                                v-model="form.username"
                                type="text"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                        form.errors.username,
                                }"
                            />

                            <p
                                v-if="form.errors.username"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.username }}
                            </p>
                        </div>

                        <!-- Name -->

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Nama
                            </label>

                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                        form.errors.name,
                                }"
                            />

                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Password -->

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Password
                                <span
                                    v-if="editMode"
                                    class="font-normal text-slate-400"
                                >
                                    (kosongkan jika tidak diubah)
                                </span>
                            </label>

                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="
                                        showPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    :placeholder="
                                        editMode
                                            ? 'Masukkan password baru'
                                            : 'Masukkan password'
                                    "
                                    autocomplete="new-password"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                    :class="{
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                            form.errors.password,
                                    }"
                                />

                                <button
                                    type="button"
                                    @click="
                                        showPassword = !showPassword
                                    "
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <svg
                                        v-if="!showPassword"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                        />
                                    </svg>

                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.394 4.667 5.446 8 10.066 8 1.3 0 2.542-.256 3.675-.72M6.228 6.228A10.45 10.45 0 0 1 12 4c4.62 0 8.673 3.333 10.066 8a10.523 10.523 0 0 1-4.132 5.411M6.228 6.228 3 3m3.228 3.228 12.544 12.544"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Password Confirmation -->

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Konfirmasi Password
                            </label>

                            <div class="relative">
                                <input
                                    v-model="
                                        form.password_confirmation
                                    "
                                    :type="
                                        showConfirmPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    placeholder="Ulangi password"
                                    autocomplete="new-password"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                    :class="{
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                            form.errors.password,
                                    }"
                                />

                                <button
                                    type="button"
                                    @click="
                                        showConfirmPassword =
                                            !showConfirmPassword
                                    "
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            v-if="!showConfirmPassword"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"
                                        />

                                        <path
                                            v-if="!showConfirmPassword"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                        />

                                        <path
                                            v-else
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.394 4.667 5.446 8 10.066 8 1.3 0 2.542-.256 3.675-.72M6.228 6.228A10.45 10.45 0 0 1 12 4c4.62 0 8.673 3.333 10.066 8a10.523 10.523 0 0 1-4.132 5.411M6.228 6.228 3 3m3.228 3.228 12.544 12.544"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Role -->

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                            >
                                Role
                            </label>

                            <select
                                v-model="form.role"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                                :class="{
                                    'border-red-500 focus:border-red-500 focus:ring-red-500/20':
                                        form.errors.role,
                                }"
                            >
                                <option value="">
                                    -- Pilih Role --
                                </option>

                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.id"
                                >
                                    {{ role.name }}
                                </option>
                            </select>

                            <p
                                v-if="form.errors.role"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.role }}
                            </p>
                        </div>


                        <!-- Actions -->

                        <div
                            class="flex justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800"
                        >
                            <button
                                type="button"
                                @click="closeModal"
                                class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    v-if="form.processing"
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
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4Z"
                                    />
                                </svg>

                                {{
                                    form.processing
                                        ? 'Menyimpan...'
                                        : editMode
                                          ? 'Simpan Perubahan'
                                          : 'Simpan'
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>