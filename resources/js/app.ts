import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const pages = import.meta.glob('./Pages/**/*.vue', {
    eager: true,
})

createInertiaApp({
    title: (title) =>
        title
            ? `${title} - ${appName}`
            : appName,

    resolve: (name) => {
        const page = pages[`./Pages/${name}.vue`]

        if (!page) {
            throw new Error(`Page not found: ./Pages/${name}.vue`)
        }

        return page
    },

    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },

    progress: {
        color: '#4B5563',
    },
})