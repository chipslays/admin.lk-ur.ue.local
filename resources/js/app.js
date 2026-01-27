import '@css/app.css';
import 'vue-sonner/style.css';

import { createApp, h, nextTick } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { ZiggyVue } from 'ziggy-js';

// Pinia
const pinia = createPinia();

pinia.use(piniaPluginPersistedstate);

// Inertia
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);

        app.config.globalProperties.window = window;

        app.mount(el);
    },
    progress: {
        delay: 300,
        color: 'var(--progress-color)',
        includeCSS: true,
        showSpinner: false,
    },
});

import Alpine from 'alpinejs';
import anchor from '@alpinejs/anchor';

Alpine.plugin(anchor);
Alpine.start();