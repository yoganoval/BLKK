<script setup>
import { ref, onMounted, watch } from 'vue'

const darkMode = ref(false)

// Fungsi untuk menerapkan tema
const applyTheme = () => {
    const isDark = darkMode.value
    
    if (isDark) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
    
    localStorage.setItem('theme', isDark ? 'dark' : 'light')
}

// Toggle tema
const toggleTheme = () => {
    darkMode.value = !darkMode.value
    applyTheme()
}

// Inisialisasi tema saat komponen dimount
onMounted(() => {
    const savedTheme = localStorage.getItem('theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    // Priority: saved theme > system preference > default light
    if (savedTheme === 'dark') {
        darkMode.value = true
    } else if (savedTheme === 'light') {
        darkMode.value = false
    } else {
        darkMode.value = prefersDark
    }
    
    applyTheme()
})

// Watch untuk perubahan tema dari luar (opsional)
watch(darkMode, () => {
    applyTheme()
})
</script>

<template>
    <button
        type="button"
        @click="toggleTheme"
        class="relative flex h-10 w-10 items-center justify-center rounded-xl
               text-slate-500 transition-all duration-200
               hover:bg-slate-100 hover:text-slate-900
               dark:text-slate-400
               dark:hover:bg-slate-800
               dark:hover:text-white
               focus:outline-none focus:ring-2 focus:ring-blue-500/50
               active:scale-95"
        :title="darkMode ? 'Mode terang' : 'Mode gelap'"
        :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
    >

        <!-- SUN (Muncul saat dark mode aktif) -->
        <svg
            v-if="darkMode"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 transition-transform duration-300 hover:rotate-90"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <circle
                cx="12"
                cy="12"
                r="4"
            />
            <path
                stroke-linecap="round"
                d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"
            />
        </svg>

        <!-- MOON (Muncul saat light mode aktif) -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 transition-transform duration-300 hover:rotate-12"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 12.79A9 9 0 1111.21 3
                   7 7 0 0021 12.79z"
            />
        </svg>

        <!-- Tooltip badge (opsional) -->
        <span 
            class="absolute -top-1 -right-1 flex h-2 w-2"
            :class="darkMode ? 'text-yellow-400' : 'text-indigo-400'"
        >
            <span 
                class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                :class="darkMode ? 'bg-yellow-400' : 'bg-indigo-400'"
            />
            <span 
                class="relative inline-flex h-2 w-2 rounded-full"
                :class="darkMode ? 'bg-yellow-400' : 'bg-indigo-400'"
            />
        </span>
    </button>
</template>