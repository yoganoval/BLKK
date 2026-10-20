<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'

import AppLogo from '@/Components/AppLogo.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'

import {
    LayoutDashboard,
    School,
    Clipboard,
    UserPlus,
    Users,
    CheckCircle,
    FileText,
    Calendar,
    Box,
    List,
    Settings,
    UserCog,
    Badge,
    Lock,
    Shield,
    Award,
} from 'lucide-vue-next'


// ============================================================
// PAGE
// ============================================================

const page = usePage()


// ============================================================
// SIDEBAR STATE
// ============================================================

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)
const profileOpen = ref(false)


// ============================================================
// DROPDOWN STATE
// ============================================================

const openMenus = ref([])


// ============================================================
// LOGOUT
// ============================================================

const logout = () => {
    router.post(route('logout'))
}


// ============================================================
// USER
// ============================================================

const user = computed(() => {
    return page.props.auth?.user ?? null
})

const userName = computed(() => {
    return user.value?.name ?? 'User'
})

const username = computed(() => {
    return user.value?.username ?? ''
})


// ============================================================
// SPATIE PERMISSIONS
// ============================================================

const roles = computed(() => {
    return user.value?.roles ?? []
})

const permissions = computed(() => {
    return user.value?.permissions ?? []
})

const can = (permission) => {
    if (!permission) {
        return true
    }

    return permissions.value.includes(permission)
}


// ============================================================
// ICON MAP
// ============================================================

const iconMap = {
    dashboard: LayoutDashboard,
    class: School,
    clipboard: Clipboard,
    'user-plus': UserPlus,
    users: Users,
    'check-circle': CheckCircle,
    report: FileText,
    calendar: Calendar,
    box: Box,
    list: List,
    settings: Settings,
    'user-cog': UserCog,
    badge: Badge,
    lock: Lock,
    shield: Shield,
    certificate: Award,
}


// ============================================================
// MENU
// ============================================================

const menus = [
    // ========================================================
    // UTAMA
    // ========================================================

    {
        type: 'menu',
        label: 'Dashboard',
        url: '/dashboard',
        permission: 'dashboard.view',
        icon: 'dashboard',
    },


    // ========================================================
    // MANAJEMEN PELATIHAN
    // ========================================================

    {
        type: 'dropdown',
        label: 'Manajemen Pelatihan',
        icon: 'class',

        children: [
            {
                label: 'Pelatihan',
                url: '/pelatihans',
                permission: 'pelatihan.view',
                icon: 'class',
            },

            {
                label: 'Kelas',
                url: '/kelas',
                permission: 'kelas.view',
                icon: 'class',
            },

            {
                label: 'Tahap',
                url: '/tahaps',
                permission: 'tahap.view',
                icon: 'clipboard',
            },
            
            {
                label: 'Program Pelatihan',
                url: '/program-pelatihans',
                permission: 'programpelatihan.view',
                icon: 'clipboard',
            },
        ],
    },
    
    
    // ========================================================
    // MANAJEMEN PESERTA
    // ========================================================
    
    {
        type: 'dropdown',
        label: 'Module Pelatihan',
        icon: 'users',
        
        children: [
            
            {
                label: 'Materi',
                url: '/materi',
                permission: 'materi.index',
                icon: 'user-plus',
            },
            {
                label: 'Tugas',
                url: '/tugas',
                permission: 'tugas.index',
                icon: 'users',
            },

            {
                label: 'Pengumpulan Tugas',
                url: '/pengumpulan-tugas',
                permission: 'pengumpulan-tugas.index',
                icon: 'certificate',
            },

            {
                label: 'Pengumpulan Tugas Peserta',
                url: '/peserta/tugas',
                permission: 'peserta-tugas.index',
                icon: 'users',
            },

            {
                label: 'Kuis',
                url: '/kuis',
                permission: 'kuis.index',
                icon: 'clipboard-list',
            },

            {
                label: 'Koreksi Esai',
                url: '/koreksi-esai',
                permission: 'koreksi-esai.index',
                icon: 'clipboard-list',
            },

            {
                label: 'Soal Kuis',
                url: '/soal-kuis',
                permission: 'soal.index',
                icon: 'file-question',
            },

            {
                label: 'Opsi Jawaban Kuis',
                url: '/opsi-jawaban-kuis',
                permission: 'opsi-jawaban.index',
                icon: 'list-check',
            },

            {
                label: 'Komponen Nilai',
                url: '/komponen-nilai',
                permission: '',
                icon: 'certificate',
            },

            {
                label: 'Nilai Peserta',
                url: '/nilai-peserta',
                permission: '',
                icon: 'users',
            },

            {
                label: 'Pengaturan Rekap Nilai',
                url: '/pengaturan-rekap-nilai',
                permission: 'pengaturan-rekap-nilai.index',
                icon: 'users',
            },
        ],
    },


    // ========================================================
    // MANAJEMEN PESERTA
    // ========================================================
    
    {
        type: 'dropdown',
        label: 'Manajemen Peserta',
        icon: 'users',
        
        children: [
            
            {
                label: 'Pendaftaran',
                url: '/pendaftarans',
                permission: 'pendaftaran.view',
                icon: 'user-plus',
            },
            {
                label: 'Peserta',
                url: '/pesertas',
                permission: 'peserta.view',
                icon: 'users',
            },

            {
                label: 'Pembayaran',
                url: '/pembayarans',
                permission: 'pembayaran.view',
                icon: 'certificate',
            },

            {
                label: 'Data Wajah',
                url: '/peserta-faces',
                permission: 'peserta-face.view',
                icon: 'users',
            },
        ],
    },


    // ========================================================
    // PRESENSI
    // ========================================================

    {
        type: 'dropdown',
        label: 'Presensi',
        icon: 'check-circle',

        children: [
            {
                label: 'Absensi',
                url: '/absensi',
                permission: 'absensi.view',
                icon: 'check-circle',
            },

            {
                label: 'Absensi Staff',
                url: '/absensi-staff',
                permission: 'absensi-staff.index',
                icon: 'check-circle',
            },

            {
                label: 'Rekap Absensi',
                url: '/rekap/absensi',
                permission: 'absensi.view',
                icon: 'report',
            },

            {
                label: 'Hari Libur',
                url: '/hari-libur',
                permission: 'hari-libur.index',
                icon: 'report',
            },

            {
                label: 'Jadwal Absensi',
                url: '/jadwals',
                permission: 'jadwal.view',
                icon: 'calendar',
            },

            {
                label: 'Jadwal Absensi Staff',
                url: '/jadwal-staff',
                permission: 'jadwal-staff.index',
                icon: 'calendar',
            },

            {
                label: 'Lokasi Absensi Staff',
                url: '/lokasi-absensi',
                permission: 'lokasi-absensi.index',
                icon: 'calendar',
            },
        ],
    },


    // ========================================================
    // DATA INVENTARIS
    // ========================================================

    {
        type: 'dropdown',
        label: 'Data Inventaris',
        icon: 'box',

        children: [
            {
                label: 'Inventaris',
                url: '/inventaris',
                permission: 'inventaris.index',
                icon: 'box',
            },

            {
                label: 'Kategori Inventaris',
                url: '/kategori-inventaris',
                permission: 'kategori-inventaris.index',
                icon: 'list',
            },
        ],
    },


    // ========================================================
    // LPJ KEUANGAN
    // ========================================================

    {
        type: 'dropdown',
        label: 'LPJ Keuangan',
        icon: 'box',

        children: [
            {
                label: 'LPJ Keuangan',
                url: '/lpj-keuangan',
                permission: 'lpj-keuangan.index',
                icon: 'box',
            },
        ],
    },


    // ========================================================
    // MANAJEMEN SISTEM
    // ========================================================

    {
        type: 'dropdown',
        label: 'Manajemen Sistem',
        icon: 'settings',

        children: [
            {
                label: 'Users',
                url: '/users',
                permission: 'user.view',
                icon: 'user-cog',
            },

            {
                label: 'Role',
                url: '/roles',
                permission: 'role.view',
                icon: 'badge',
            },

            {
                label: 'Permissions',
                url: '/permissions',
                permission: 'permission.view',
                icon: 'lock',
            },

            {
                label: 'Role Permissions',
                url: '/role-permissions',
                permission: 'rolepermissions.view',
                icon: 'shield',
            },
        ],
    },
]


// ============================================================
// FILTER MENU BERDASARKAN PERMISSION
// ============================================================

const visibleMenus = computed(() => {
    return menus
        .map((menu) => {

            // --------------------------------------------------
            // MENU BIASA
            // --------------------------------------------------

            if (menu.type === 'menu') {

                if (!menu.permission || can(menu.permission)) {
                    return menu
                }

                return null
            }


            // --------------------------------------------------
            // DROPDOWN
            // --------------------------------------------------

            if (menu.type === 'dropdown') {

                const children = (menu.children || []).filter(
                    (child) => {
                        return !child.permission || can(child.permission)
                    }
                )

                // Tidak ada child yang boleh dilihat
                if (children.length === 0) {
                    return null
                }

                return {
                    ...menu,
                    children,
                }
            }

            return menu
        })
        .filter(Boolean)
})


// ============================================================
// ACTIVE URL
// ============================================================

const isActive = (url) => {

    if (!url) {
        return false
    }

    const currentUrl = page.url.split('?')[0]

    // Dashboard harus exact
    if (url === '/dashboard') {
        return currentUrl === '/dashboard'
    }

    return (
        currentUrl === url ||
        currentUrl.startsWith(`${url}/`)
    )
}


// ============================================================
// ACTIVE CHILD
// ============================================================

const hasActiveChild = (menu) => {

    if (!menu || menu.type !== 'dropdown') {
        return false
    }

    return (menu.children || []).some((child) => {
        return child.url && isActive(child.url)
    })
}


// ============================================================
// PRIMARY ROLE
// ============================================================

const primaryRole = computed(() => {

    if (!roles.value.length) {
        return 'User'
    }

    return roles.value[0]
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (letter) => {
            return letter.toUpperCase()
        })
})


// ============================================================
// TOGGLE DROPDOWN
// ============================================================

const toggleMenu = (label) => {

    if (openMenus.value.includes(label)) {

        // Klik menu yang sama → tutup
        openMenus.value = []

    } else {

        // Hanya satu dropdown terbuka
        openMenus.value = [label]
    }
}


// ============================================================
// CLOSE DROPDOWNS
// ============================================================

const closeDropdowns = () => {
    openMenus.value = []
}


// ============================================================
// MOBILE SIDEBAR
// ============================================================

const closeMobileSidebar = () => {

    sidebarOpen.value = false

    // Tutup dropdown juga
    closeDropdowns()
}


// ============================================================
// SIDEBAR COLLAPSE
// ============================================================

const toggleSidebar = () => {

    sidebarCollapsed.value = !sidebarCollapsed.value

    // Jika collapse → tutup dropdown
    if (sidebarCollapsed.value) {
        closeDropdowns()
    }
}

</script>


<template>

    <Head />


    <!-- ====================================================== -->
    <!-- ROOT -->
    <!-- ====================================================== -->

    <div
        class="min-h-screen bg-slate-50 text-slate-900 transition-colors duration-200 dark:bg-slate-950 dark:text-slate-100"
    >


        <!-- ================================================== -->
        <!-- MOBILE OVERLAY -->
        <!-- ================================================== -->

        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >

            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
                @click="closeMobileSidebar"
            />

        </Transition>


        <!-- ================================================== -->
        <!-- SIDEBAR -->
        <!-- ================================================== -->

        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-slate-200 bg-white transition-all duration-300 dark:border-slate-800 dark:bg-slate-900"
            :class="[
                sidebarCollapsed
                    ? 'lg:w-20'
                    : 'w-72',

                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0'
            ]"
        >


            <!-- ============================================== -->
            <!-- LOGO -->
            <!-- ============================================== -->

            <div
                class="flex h-20 shrink-0 items-center border-b border-slate-200 transition-all duration-300 dark:border-slate-800"
                :class="
                    sidebarCollapsed
                        ? 'lg:justify-center lg:px-2'
                        : 'justify-between px-4'
                "
            >

                <Link
                    href="/dashboard"
                    class="flex min-w-0 items-center"
                    @click="closeMobileSidebar"
                >
                    <AppLogo
                        :collapsed="sidebarCollapsed"
                    />
                </Link>


                <!-- MOBILE CLOSE -->

                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white lg:hidden"
                    @click="closeMobileSidebar"
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
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                </button>

            </div>


            <!-- ================================================= -->
            <!-- NAVIGATION -->
            <!-- ================================================= -->

            <nav
                class="sidebar-scroll flex-1 overflow-y-auto overflow-x-hidden px-3 py-5"
            >

                <template
                    v-for="menu in visibleMenus"
                    :key="menu.label"
                >


                    <!-- ========================================= -->
                    <!-- MENU BIASA -->
                    <!-- ========================================= -->

                    <Link
                        v-if="menu.type === 'menu'"
                        :href="menu.url"
                        @click="closeMobileSidebar"
                        class="group relative mb-1.5 flex w-full items-center overflow-hidden rounded-xl py-3 text-sm font-medium transition-all duration-200 ease-out"
                        :class="[
                            sidebarCollapsed
                                ? 'lg:justify-center lg:px-0'
                                : 'px-3',

                            isActive(menu.url)
                                ? [
                                    'bg-gradient-to-r from-indigo-50 to-indigo-100/60',
                                    'text-indigo-600 shadow-sm',
                                    'dark:from-indigo-500/15 dark:to-indigo-500/5',
                                    'dark:text-indigo-400'
                                ]
                                : [
                                    'text-slate-600',
                                    'hover:-translate-y-[1px]',
                                    'hover:bg-slate-100',
                                    'hover:text-slate-900',
                                    'hover:shadow-sm',
                                    'dark:text-slate-400',
                                    'dark:hover:bg-slate-800/80',
                                    'dark:hover:text-white'
                                ]
                        ]"
                    >


                        <!-- ACTIVE INDICATOR -->

                        <span
                            v-if="isActive(menu.url)"
                            class="absolute left-0 top-1/2 h-7 w-1 -translate-y-1/2 rounded-r-full bg-indigo-600 dark:bg-indigo-400"
                        />


                        <!-- ICON -->

                        <div
                            class="relative flex h-6 w-6 shrink-0 items-center justify-center transition-all duration-200"
                            :class="
                                isActive(menu.url)
                                    ? 'text-indigo-600 dark:text-indigo-400'
                                    : 'text-slate-500 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400'
                            "
                        >

                            <component
                                v-if="
                                    menu.icon &&
                                    iconMap[menu.icon]
                                "
                                :is="iconMap[menu.icon]"
                                class="h-5 w-5"
                            />

                        </div>


                        <!-- LABEL -->

                        <span
                            v-if="!sidebarCollapsed"
                            class="ml-3 min-w-0 flex-1 truncate"
                        >
                            {{ menu.label }}
                        </span>


                        <!-- ACTIVE DOT -->

                        <span
                            v-if="
                                !sidebarCollapsed &&
                                isActive(menu.url)
                            "
                            class="mr-1 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600 dark:bg-indigo-400"
                        />

                    </Link>


                    <!-- ========================================= -->
                    <!-- DROPDOWN -->
                    <!-- ========================================= -->

                    <div
                        v-else-if="menu.type === 'dropdown'"
                        class="mb-1.5"
                    >


                        <!-- ===================================== -->
                        <!-- DROPDOWN HEADER -->
                        <!-- ===================================== -->

                        <button
                            type="button"
                            @click="toggleMenu(menu.label)"
                            class="group relative flex w-full items-center overflow-hidden rounded-xl py-3 text-sm font-medium transition-all duration-200 ease-out"
                            :class="[
                                sidebarCollapsed
                                    ? 'lg:justify-center lg:px-0'
                                    : 'px-3',

                                hasActiveChild(menu)
                                    ? [
                                        'bg-gradient-to-r from-indigo-50 to-indigo-100/60',
                                        'text-indigo-600 shadow-sm',
                                        'dark:from-indigo-500/15 dark:to-indigo-500/5',
                                        'dark:text-indigo-400'
                                    ]
                                    : [
                                        'text-slate-600',
                                        'hover:-translate-y-[1px]',
                                        'hover:bg-slate-100',
                                        'hover:text-slate-900',
                                        'hover:shadow-sm',
                                        'dark:text-slate-400',
                                        'dark:hover:bg-slate-800/80',
                                        'dark:hover:text-white'
                                    ]
                            ]"
                        >


                            <!-- ACTIVE INDICATOR -->

                            <span
                                v-if="hasActiveChild(menu)"
                                class="absolute left-0 top-1/2 h-7 w-1 -translate-y-1/2 rounded-r-full bg-indigo-600 dark:bg-indigo-400"
                            />


                            <!-- DROPDOWN ICON -->

                            <div
                                class="relative flex h-6 w-6 shrink-0 items-center justify-center transition-all duration-200"
                                :class="
                                    hasActiveChild(menu)
                                        ? 'text-indigo-600 dark:text-indigo-400'
                                        : 'text-slate-500 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400'
                                "
                            >

                                <!--
                                    PENTING:
                                    Header memakai menu.icon,
                                    BUKAN child.icon
                                -->

                                <component
                                    v-if="
                                        menu.icon &&
                                        iconMap[menu.icon]
                                    "
                                    :is="iconMap[menu.icon]"
                                    class="h-5 w-5"
                                />

                            </div>


                            <!-- LABEL -->

                            <span
                                v-if="!sidebarCollapsed"
                                class="ml-3 flex-1 truncate text-left"
                            >
                                {{ menu.label }}
                            </span>


                            <!-- ACTIVE DOT -->

                            <span
                                v-if="
                                    !sidebarCollapsed &&
                                    hasActiveChild(menu)
                                "
                                class="mr-2 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600 dark:bg-indigo-400"
                            />


                            <!-- CHEVRON -->

                            <svg
                                v-if="!sidebarCollapsed"
                                class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-300 ease-out group-hover:text-indigo-500"
                                :class="
                                    openMenus.includes(menu.label)
                                        ? 'rotate-180 text-indigo-500'
                                        : ''
                                "
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7"
                                />

                            </svg>

                        </button>


                        <!-- ===================================== -->
                        <!-- CHILDREN -->
                        <!-- ===================================== -->

                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="max-h-0 opacity-0 -translate-y-1"
                            enter-to-class="max-h-[500px] opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="max-h-[500px] opacity-100 translate-y-0"
                            leave-to-class="max-h-0 opacity-0 -translate-y-1"
                        >

                            <div
                                v-if="
                                    !sidebarCollapsed &&
                                    openMenus.includes(menu.label)
                                "
                                class="relative ml-3 mt-1.5 space-y-1 overflow-hidden border-l border-slate-200/80 pl-3 dark:border-slate-700/80"
                            >

                                <!-- GARIS VERTIKAL -->

                                <span
                                    class="pointer-events-none absolute -left-px top-0 h-full w-px bg-gradient-to-b from-indigo-200 via-slate-200 to-transparent dark:from-indigo-500/40 dark:via-slate-700 dark:to-transparent"
                                />


                                <!-- CHILD MENU -->

                                <Link
                                    v-for="child in menu.children"
                                    :key="child.label"
                                    :href="child.url"
                                    @click="closeMobileSidebar"
                                    class="group relative flex items-center rounded-lg px-3 py-2.5 text-sm transition-all duration-200 ease-out"
                                    :class="
                                        isActive(child.url)
                                            ? [
                                                'bg-indigo-50 font-semibold text-indigo-600',
                                                'shadow-sm',
                                                'dark:bg-indigo-500/10',
                                                'dark:text-indigo-400'
                                            ]
                                            : [
                                                'text-slate-500',
                                                'hover:translate-x-1',
                                                'hover:bg-slate-100',
                                                'hover:text-slate-900',
                                                'dark:text-slate-400',
                                                'dark:hover:bg-slate-800/80',
                                                'dark:hover:text-white'
                                            ]
                                    "
                                >

                                    <!-- ACTIVE CHILD INDICATOR -->

                                    <span
                                        v-if="isActive(child.url)"
                                        class="absolute -left-[13px] top-1/2 h-6 w-0.5 -translate-y-1/2 rounded-full bg-indigo-600 dark:bg-indigo-400"
                                    />


                                    <!-- CHILD ICON -->

                                    <div
                                        class="flex h-5 w-5 shrink-0 items-center justify-center transition-all duration-200"
                                        :class="
                                            isActive(child.url)
                                                ? 'text-indigo-600 dark:text-indigo-400'
                                                : 'text-slate-400 group-hover:text-indigo-500 dark:text-slate-500 dark:group-hover:text-indigo-400'
                                        "
                                    >

                                        <component
                                            v-if="
                                                child.icon &&
                                                iconMap[child.icon]
                                            "
                                            :is="iconMap[child.icon]"
                                            class="h-4 w-4"
                                        />

                                    </div>


                                    <!-- CHILD LABEL -->

                                    <span
                                        class="ml-3 min-w-0 flex-1 truncate"
                                    >
                                        {{ child.label }}
                                    </span>


                                    <!-- ACTIVE DOT -->

                                    <span
                                        v-if="isActive(child.url)"
                                        class="h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-600 dark:bg-indigo-400"
                                    />

                                </Link>

                            </div>

                        </Transition>

                    </div>

                </template>

            </nav>


            <!-- ================================================= -->
            <!-- USER AREA -->
            <!-- ================================================= -->

            <div
                class="shrink-0 border-t border-slate-200 p-3 dark:border-slate-800"
            >

                <div
                    class="flex items-center rounded-xl bg-slate-50 p-2.5 transition-all duration-300 dark:bg-slate-800/60"
                    :class="
                        sidebarCollapsed
                            ? 'lg:justify-center lg:px-1'
                            : ''
                    "
                >

                    <!-- AVATAR -->

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-bold text-white shadow-lg shadow-indigo-600/20"
                    >
                        {{ userName.charAt(0).toUpperCase() }}
                    </div>


                    <!-- USER INFO -->

                    <div
                        v-if="!sidebarCollapsed"
                        class="ml-3 min-w-0 flex-1"
                    >

                        <p
                            class="truncate text-sm font-semibold text-slate-800 dark:text-white"
                        >
                            {{ userName }}
                        </p>

                        <p
                            class="mt-0.5 truncate text-xs capitalize text-slate-500 dark:text-slate-400"
                        >
                            {{ primaryRole }}
                        </p>

                    </div>

                </div>

            </div>

        </aside>


        <!-- ====================================================== -->
        <!-- MAIN -->
        <!-- ====================================================== -->

        <div
            class="min-h-screen pt-20 transition-all duration-300"
            :class="[
                sidebarCollapsed
                    ? 'lg:pl-20'
                    : 'lg:pl-72',

                'pl-0'
            ]"
        >


            <!-- ================================================= -->
            <!-- TOPBAR -->
            <!-- ================================================= -->

            <header
                class="fixed left-0 right-0 top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur-xl transition-all duration-300 dark:border-slate-800 dark:bg-slate-900/90 sm:px-6"
                :class="[
                    sidebarCollapsed
                        ? 'lg:left-20'
                        : 'lg:left-72'
                ]"
            >

                <!-- LEFT TOPBAR -->

                <div class="flex items-center gap-3">


                    <!-- MOBILE MENU -->

                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white lg:hidden"
                        @click="sidebarOpen = true"
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
                                d="M4 6h16M4 12h16M4 18h16"
                            />

                        </svg>

                    </button>


                    <!-- DESKTOP COLLAPSE -->

                    <button
                        type="button"
                        class="hidden h-10 w-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white lg:flex"
                        @click="toggleSidebar"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transition-transform duration-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                            :class="
                                sidebarCollapsed
                                    ? 'rotate-180'
                                    : ''
                            "
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11 19l-7-7 7-7"
                            />

                        </svg>

                    </button>


                    <!-- TITLE -->

                    <div class="hidden sm:block">

                        <p
                            class="text-sm font-semibold text-slate-800 dark:text-white"
                        >
                            BLK Komunitas Multimedia
                        </p>

                        <p class="text-xs text-slate-400">
                            Balai Latihan Kerja Komunitas
                        </p>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- RIGHT TOPBAR -->
                <!-- ================================================= -->

                <div class="flex items-center gap-2">


                    <!-- SYSTEM STATUS -->

                    <div
                        class="hidden items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 sm:flex"
                    >

                        <span
                            class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"
                        />

                        Sistem Aktif

                    </div>


                    <!-- THEME -->

                    <ThemeToggle />


                    <!-- USER PROFILE -->

                    <div class="relative">

                        <button
                            type="button"
                            class="group flex items-center gap-2.5 rounded-2xl border border-transparent px-2 py-1.5 transition-all duration-200 hover:border-slate-200 hover:bg-slate-50 dark:hover:border-slate-700 dark:hover:bg-slate-800/70"
                            @click="
                                profileOpen = !profileOpen
                            "
                        >

                            <!-- AVATAR -->

                            <div
                                class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-bold text-white shadow-sm ring-2 ring-white transition-transform duration-200 group-hover:scale-105 dark:ring-slate-900"
                            >

                                {{ userName.charAt(0).toUpperCase() }}

                                <span
                                    class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900"
                                />

                            </div>


                            <!-- USER -->

                            <div
                                class="hidden min-w-0 text-left sm:block"
                            >

                                <p
                                    class="max-w-40 truncate text-sm font-semibold leading-5 text-slate-800 dark:text-slate-100"
                                    :title="userName"
                                >
                                    {{ userName }}
                                </p>

                                <p
                                    class="mt-0.5 truncate text-[11px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500"
                                >
                                    {{ primaryRole }}
                                </p>

                            </div>


                            <!-- CHEVRON -->

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="hidden h-4 w-4 text-slate-400 transition-transform duration-200 sm:block"
                                :class="
                                    profileOpen
                                        ? 'rotate-180'
                                        : ''
                                "
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 9l6 6 6-6"
                                />

                            </svg>

                        </button>


                        <!-- ================================================= -->
                        <!-- PROFILE DROPDOWN -->
                        <!-- ================================================= -->

                        <Transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="scale-95 -translate-y-1 opacity-0"
                            enter-to-class="scale-100 translate-y-0 opacity-100"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="scale-100 translate-y-0 opacity-100"
                            leave-to-class="scale-95 -translate-y-1 opacity-0"
                        >

                            <div
                                v-if="profileOpen"
                                class="absolute right-0 top-[calc(100%+10px)] z-50 w-72 origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30"
                            >

                                <!-- PROFILE HEADER -->

                                <div
                                    class="border-b border-slate-100 bg-slate-50/70 px-4 py-4 dark:border-slate-800 dark:bg-slate-800/40"
                                >

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-base font-bold text-white shadow-sm"
                                        >
                                            {{ userName.charAt(0).toUpperCase() }}
                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <p
                                                class="truncate text-sm font-bold text-slate-800 dark:text-white"
                                                :title="userName"
                                            >
                                                {{ userName }}
                                            </p>

                                            <p
                                                class="mt-0.5 truncate text-xs text-slate-400"
                                                :title="'@' + username"
                                            >
                                                @{{ username }}
                                            </p>

                                        </div>

                                    </div>


                                    <span
                                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold capitalize text-indigo-600 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-400"
                                    >

                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-indigo-500"
                                        />

                                        {{ primaryRole }}

                                    </span>

                                </div>


                                <!-- PROFILE MENU -->

                                <div class="p-2">

                                    <Link
                                        href="/profile"
                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                                        @click="profileOpen = false"
                                    >

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
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
                                                    d="M4.5 20.25a8.25 8.25 0 0115 0"
                                                />

                                            </svg>

                                        </span>

                                        <span class="flex-1">
                                            Profil Saya
                                        </span>

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-slate-300 dark:text-slate-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 5l7 7-7 7"
                                            />

                                        </svg>

                                    </Link>


                                    <div
                                        class="my-2 border-t border-slate-100 dark:border-slate-800"
                                    />


                                    <!-- LOGOUT -->

                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                        @click="logout"
                                    >

                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12H3m0 0l4-4m-4 4l4 4"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M14 4h4a2 2 0 012 2v12a2 2 0 01-2 2h-4"
                                                />

                                            </svg>

                                        </span>

                                        <span>
                                            Keluar
                                        </span>

                                    </button>

                                </div>

                            </div>

                        </Transition>

                    </div>

                </div>

            </header>


            <!-- ================================================= -->
            <!-- CONTENT -->
            <!-- ================================================= -->

            <main
                class="min-h-[calc(100vh-5rem)] p-4 sm:p-6 lg:p-8"
            >

                <!-- PAGE HEADER -->

                <div
                    v-if="$slots.header"
                    class="mb-6"
                >
                    <slot name="header" />
                </div>


                <!-- PAGE CONTENT -->

                <slot />

            </main>

        </div>

    </div>

</template>


<style scoped>

.sidebar-scroll {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.sidebar-scroll::-webkit-scrollbar {
    display: none;
}

</style>