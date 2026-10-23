
<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import AppLogo from '@/Components/AppLogo.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'

import {
    LayoutDashboard,
    BookOpen,
    ClipboardList,
    Upload,
    Brain,
    ChartNoAxesColumnIncreasing,
    Award,
    ChevronDown,
    PanelLeftClose,
    Menu as MenuIcon,
    X,
    UserRound,
    LogOut,
    GraduationCap,
    Home,
} from 'lucide-vue-next'

defineOptions({
    name: 'PesertaLayout',
})

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    logoutRoute: {
        type: String,
        default: 'logout',
    },
})

const page = usePage()

// ============================================================
// STATE
// ============================================================

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)
const profileOpen = ref(false)
const openMenus = ref([])

// ============================================================
// DATA PESERTA
// ============================================================

const peserta = computed(() => {
    return (
        page.props.peserta ??
        page.props.auth?.peserta ??
        page.props.auth?.user ??
        {}
    )
})

const namaPeserta = computed(() => {
    return peserta.value?.nama ??
        peserta.value?.name ??
        'Peserta'
})

const inisialPeserta = computed(() => {
    return namaPeserta.value.trim().charAt(0).toUpperCase() || 'P'
})

// ============================================================
// MENU PESERTA — TANPA PERMISSION
// ============================================================

const menus = [
    {
        label: 'Dashboard',
        url: '/peserta/dashboard',
        icon: LayoutDashboard,
    },
    {
        label: 'Pembelajaran',
        type: 'dropdown',
        icon: BookOpen,
        children: [
            {
                label: 'Materi Belajar',
                url: '/peserta/materi',
                icon: BookOpen,
            },
            {
                label: 'Tugas Saya',
                url: '/peserta/tugas',
                icon: ClipboardList,
            },
            {
                label: 'Pengumpulan Tugas',
                url: '/peserta/pengumpulan-tugas',
                icon: Upload,
            },
            {
                label: 'Kuis',
                url: '/peserta/kuis',
                icon: Brain,
            },
        ],
    },
    {
        label: 'Progres Belajar',
        url: '/peserta/progres',
        icon: ChartNoAxesColumnIncreasing,
    },
    {
        label: 'Absen Peserta',
        url: '/peserta/absensi',
        icon: ChartNoAxesColumnIncreasing,
    },
    {
        label: 'Sertifikat',
        url: '/peserta/sertifikat',
        icon: Award,
    },
]

// ============================================================
// ACTIVE URL
// ============================================================

const currentUrl = computed(() => {
    return page.url.split('?')[0].replace(/\/$/, '') || '/'
})

const isActive = (url) => {
    const target = url.replace(/\/$/, '') || '/'

    if (target === '/peserta/dashboard') {
        return currentUrl.value === target ||
            currentUrl.value === '/peserta'
    }

    return currentUrl.value === target ||
        currentUrl.value.startsWith(`${target}/`)
}

const hasActiveChild = (menu) => {
    return menu.children?.some(child => isActive(child.url)) ?? false
}

// ============================================================
// DROPDOWN
// ============================================================

const toggleMenu = (label) => {
    openMenus.value = openMenus.value.includes(label)
        ? []
        : [label]
}

const closeMobileSidebar = () => {
    sidebarOpen.value = false
    profileOpen.value = false
}

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value

    if (sidebarCollapsed.value) {
        openMenus.value = []
    }
}

// Buka dropdown otomatis sesuai halaman yang sedang dikunjungi.
const syncActiveMenu = () => {
    const activeMenu = menus.find(menu => hasActiveChild(menu))

    if (activeMenu && !sidebarCollapsed.value) {
        openMenus.value = [activeMenu.label]
    }
}

const onKeydown = (event) => {
    if (event.key === 'Escape') {
        sidebarOpen.value = false
        profileOpen.value = false
        openMenus.value = []
    }
}

onMounted(() => {
    syncActiveMenu()
    window.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown)
})

// ============================================================
// LOGOUT
// ============================================================

const logout = () => {
    router.post(route(props.logoutRoute))
}
</script>

<template>
    <Head :title="`${title} | BLKK Learning`" />

    <div class="min-h-screen bg-slate-50 text-slate-900 transition-colors duration-200 dark:bg-slate-950 dark:text-slate-100">

        <!-- OVERLAY MOBILE -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <button
                v-if="sidebarOpen"
                type="button"
                aria-label="Tutup navigasi"
                class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
                @click="closeMobileSidebar"
            />
        </Transition>

        <!-- SIDEBAR -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-slate-200 bg-white transition-all duration-300 dark:border-slate-800 dark:bg-slate-900"
            :class="[
                sidebarCollapsed ? 'lg:w-20' : 'w-72',
                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full lg:translate-x-0',
            ]"
        >
            <!-- LOGO -->
            <div
                class="flex h-20 shrink-0 items-center border-b border-slate-200 dark:border-slate-800"
                :class="sidebarCollapsed
                    ? 'justify-between px-3 lg:justify-center'
                    : 'justify-between px-4'"
            >
                <Link
                    href="/peserta/dashboard"
                    class="flex min-w-0 items-center"
                    @click="closeMobileSidebar"
                >
                    <AppLogo :collapsed="sidebarCollapsed" />
                </Link>

                <button
                    type="button"
                    aria-label="Tutup sidebar"
                    class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                    @click="closeMobileSidebar"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- LABEL -->
            <div
                v-if="!sidebarCollapsed"
                class="px-6 pb-2 pt-6 text-[10px] font-bold tracking-[1.4px] text-slate-400"
            >
                MENU PEMBELAJARAN
            </div>

            <!-- NAVIGATION -->
            <nav class="sidebar-scroll flex-1 space-y-1 overflow-y-auto overflow-x-hidden px-3 py-4">
                <template v-for="menu in menus" :key="menu.label">

                    <!-- MENU BIASA -->
                    <Link
                        v-if="!menu.type"
                        :href="menu.url"
                        :title="sidebarCollapsed ? menu.label : undefined"
                        class="group relative flex min-h-11 items-center overflow-hidden rounded-xl py-3 text-sm font-medium transition-all duration-200"
                        :class="[
                            sidebarCollapsed
                                ? 'justify-center px-0'
                                : 'px-3',
                            isActive(menu.url)
                                ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white',
                        ]"
                        @click="closeMobileSidebar"
                    >
                        <span
                            v-if="isActive(menu.url)"
                            class="absolute bottom-2 left-0 top-2 w-1 rounded-r-full bg-indigo-600 dark:bg-indigo-400"
                        />

                        <component
                            :is="menu.icon"
                            class="h-5 w-5 shrink-0"
                        />

                        <span
                            v-if="!sidebarCollapsed"
                            class="ml-3 min-w-0 flex-1 truncate"
                        >
                            {{ menu.label }}
                        </span>
                    </Link>

                    <!-- DROPDOWN -->
                    <div v-else>
                        <button
                            type="button"
                            :title="sidebarCollapsed ? menu.label : undefined"
                            class="group flex min-h-11 w-full items-center rounded-xl py-3 text-sm font-medium transition-all duration-200"
                            :class="[
                                sidebarCollapsed
                                    ? 'justify-center px-0'
                                    : 'px-3',
                                hasActiveChild(menu)
                                    ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white',
                            ]"
                            @click="sidebarCollapsed
                                ? (sidebarCollapsed = false, openMenus = [menu.label])
                                : toggleMenu(menu.label)"
                        >
                            <component
                                :is="menu.icon"
                                class="h-5 w-5 shrink-0"
                            />

                            <span
                                v-if="!sidebarCollapsed"
                                class="ml-3 min-w-0 flex-1 truncate text-left"
                            >
                                {{ menu.label }}
                            </span>

                            <ChevronDown
                                v-if="!sidebarCollapsed"
                                class="h-4 w-4 shrink-0 transition-transform duration-200"
                                :class="openMenus.includes(menu.label) ? 'rotate-180' : ''"
                            />
                        </button>

                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="max-h-0 -translate-y-1 opacity-0"
                            enter-to-class="max-h-96 translate-y-0 opacity-100"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="max-h-96 translate-y-0 opacity-100"
                            leave-to-class="max-h-0 -translate-y-1 opacity-0"
                        >
                            <div
                                v-if="!sidebarCollapsed && openMenus.includes(menu.label)"
                                class="ml-5 mt-1 space-y-1 overflow-hidden border-l border-slate-200 pl-3 dark:border-slate-700"
                            >
                                <Link
                                    v-for="child in menu.children"
                                    :key="child.label"
                                    :href="child.url"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-medium transition-colors"
                                    :class="isActive(child.url)
                                        ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'
                                        : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white'"
                                    @click="closeMobileSidebar"
                                >
                                    <component
                                        :is="child.icon"
                                        class="h-4 w-4 shrink-0"
                                    />
                                    <span class="min-w-0 flex-1 truncate">
                                        {{ child.label }}
                                    </span>
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </template>
            </nav>

            <!-- USER SIDEBAR -->
            <div class="shrink-0 border-t border-slate-200 p-3 dark:border-slate-800">
                <div
                    class="flex items-center rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800/60"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-bold text-white">
                        {{ inisialPeserta }}
                    </div>

                    <div v-if="!sidebarCollapsed" class="ml-3 min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">
                            {{ namaPeserta }}
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Peserta pelatihan
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <div
            class="min-h-screen transition-all duration-300"
            :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
        >
            <!-- TOPBAR -->
            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90 sm:px-6"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <!-- MOBILE MENU -->
                    <button
                        type="button"
                        aria-label="Buka navigasi"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                        @click="sidebarOpen = true"
                    >
                        <MenuIcon class="h-5 w-5" />
                    </button>

                    <!-- COLLAPSE DESKTOP -->
                    <button
                        type="button"
                        aria-label="Ciutkan atau lebarkan sidebar"
                        class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:flex"
                        @click="toggleSidebar"
                    >
                        <PanelLeftClose
                            class="h-5 w-5 transition-transform"
                            :class="sidebarCollapsed ? 'rotate-180' : ''"
                        />
                    </button>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">
                            BLKK Learning Portal
                        </p>
                        <p class="truncate text-xs text-slate-400">
                            Ruang belajar peserta
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <div class="hidden items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 sm:flex">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500" />
                        Siap Belajar
                    </div>

                    <ThemeToggle />

                    <!-- PROFILE DROPDOWN -->
                    <div class="relative">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-xl border border-transparent p-1.5 transition hover:border-slate-200 hover:bg-slate-50 dark:hover:border-slate-700 dark:hover:bg-slate-800"
                            :aria-expanded="profileOpen"
                            @click="profileOpen = !profileOpen"
                        >
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-bold text-white">
                                {{ inisialPeserta }}
                            </div>

                            <span class="hidden max-w-36 truncate text-sm font-semibold sm:block">
                                {{ namaPeserta }}
                            </span>

                            <ChevronDown class="hidden h-4 w-4 text-slate-400 sm:block" />
                        </button>

                        <Transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="translate-y-1 scale-95 opacity-0"
                            enter-to-class="translate-y-0 scale-100 opacity-100"
                            leave-active-class="transition duration-100 ease-in"
                            leave-from-class="translate-y-0 scale-100 opacity-100"
                            leave-to-class="translate-y-1 scale-95 opacity-0"
                        >
                            <div
                                v-if="profileOpen"
                                class="absolute right-0 top-[calc(100%+10px)] z-50 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900"
                            >
                                <div class="border-b border-slate-100 px-4 py-4 dark:border-slate-800">
                                    <p class="truncate text-sm font-bold">
                                        {{ namaPeserta }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Akun peserta pelatihan
                                    </p>
                                </div>

                                <div class="p-2">
                                    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-600 dark:text-slate-300">
                                        <UserRound class="h-4 w-4" />
                                        <span>Profil Peserta</span>
                                    </div>

                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                        @click="logout"
                                    >
                                        <LogOut class="h-4 w-4" />
                                        Keluar
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="min-h-[calc(100vh-5rem)] p-4 sm:p-6 lg:p-8">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>

                <slot />
            </main>

            <footer class="flex flex-wrap justify-between gap-2 border-t border-slate-200 px-5 py-4 text-xs text-slate-400 dark:border-slate-800 sm:px-8">
                <span>© {{ new Date().getFullYear() }} BLKK Learning Portal</span>
                <span>Belajar · Berkembang · Berprestasi</span>
            </footer>
        </div>
    </div>
</template>

<style scoped>
.sidebar-scroll {
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
}

.sidebar-scroll:hover {
    scrollbar-color: #cbd5e1 transparent;
}

.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    border-radius: 10px;
    background: transparent;
}

.sidebar-scroll:hover::-webkit-scrollbar-thumb {
    background: #cbd5e1;
}
</style>
