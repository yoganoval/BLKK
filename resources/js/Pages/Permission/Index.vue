<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    permissions: {
        type: Object,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0,
            links: [],
        }),
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            perPage: 10,
        }),
    },
})

const search = ref(props.filters.search ?? '')
const perPage = ref(props.filters.perPage ?? 10)

let searchTimeout = null

watch(search, (value) => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        router.get(
            route('permissions.index'),
            {
                search: value,
                perPage: perPage.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        )
    }, 400)
})

function changePerPage() {
    router.get(
        route('permissions.index'),
        {
            search: search.value,
            perPage: perPage.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

function deletePermission(permission) {
    if (!confirm(`Hapus permission "${permission.name}"?`)) {
        return
    }

    router.delete(
        route('permissions.destroy', permission.id),
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <Head title="Permission" />

    <AdminLayout>

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">

            <div>
                <h1
                    class="text-2xl font-bold text-slate-900 dark:text-white"
                >
                    Permission
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                >
                    Kelola permission sistem.
                </p>
            </div>

            <Link
                :href="route('permissions.create')"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
            >
                <span class="text-lg leading-none">+</span>
                Tambah Permission
            </Link>

        </div>


        <!-- CARD -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
        >

            <!-- TOOLBAR -->
            <div
                class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700"
            >

                <!-- SEARCH -->
                <div class="relative w-full sm:max-w-sm">

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari permission..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    />

                </div>


                <!-- PER PAGE -->
                <div class="flex items-center gap-2">

                    <span
                        class="text-sm text-slate-500 dark:text-slate-400"
                    >
                        Tampilkan
                    </span>

                    <select
                        v-model="perPage"
                        @change="changePerPage"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                    >
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>

                </div>

            </div>


            <!-- TABLE -->
            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead
                        class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400"
                    >
                        <tr>
                            <th class="px-6 py-4">
                                #
                            </th>

                            <th class="px-6 py-4">
                                Permission
                            </th>

                            <th class="px-6 py-4">
                                Guard
                            </th>

                            <th class="px-6 py-4 text-right">
                                Aksi
                            </th>
                        </tr>
                    </thead>


                    <tbody
                        class="divide-y divide-slate-200 dark:divide-slate-700"
                    >

                        <tr
                            v-for="(permission, index) in permissions.data"
                            :key="permission.id"
                            class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >

                            <td
                                class="whitespace-nowrap px-6 py-4 text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    (permissions.current_page - 1)
                                    * permissions.per_page
                                    + index
                                    + 1
                                }}
                            </td>


                            <td class="px-6 py-4">

                                <div
                                    class="font-semibold text-slate-900 dark:text-white"
                                >
                                    {{ permission.name }}
                                </div>

                            </td>


                            <td class="px-6 py-4">

                                <span
                                    class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                                >
                                    {{ permission.guard_name }}
                                </span>

                            </td>


                            <td class="px-6 py-4">

                                <div
                                    class="flex justify-end gap-2"
                                >

                                    <Link
                                        :href="
                                            route(
                                                'permissions.edit',
                                                permission.id
                                            )
                                        "
                                        class="rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                                    >
                                        Edit
                                    </Link>


                                    <button
                                        type="button"
                                        @click="deletePermission(permission)"
                                        class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->
                        <tr v-if="permissions.data.length === 0">

                            <td
                                colspan="4"
                                class="px-6 py-12 text-center"
                            >

                                <div
                                    class="text-sm text-slate-500 dark:text-slate-400"
                                >
                                    Tidak ada permission ditemukan.
                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- FOOTER -->
            <div
                class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700"
            >

                <div
                    class="text-sm text-slate-500 dark:text-slate-400"
                >
                    Menampilkan
                    <span class="font-semibold text-slate-700 dark:text-white">
                        {{ permissions.from ?? 0 }}
                    </span>
                    -
                    <span class="font-semibold text-slate-700 dark:text-white">
                        {{ permissions.to ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-slate-700 dark:text-white">
                        {{ permissions.total ?? 0 }}
                    </span>
                    permission
                </div>


                <!-- PAGINATION -->
                <div class="flex flex-wrap gap-1">

                    <template
                        v-for="link in permissions.links"
                        :key="link.label"
                    >

                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            preserve-state
                            class="rounded-lg px-3 py-2 text-sm transition"
                            :class="
                                link.active
                                    ? 'bg-emerald-600 text-white'
                                    : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                            "
                        >
                            <span v-html="link.label" />
                        </Link>

                        <span
                            v-else
                            class="rounded-lg px-3 py-2 text-sm text-slate-300 dark:text-slate-600"
                        >
                            <span v-html="link.label" />
                        </span>

                    </template>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>