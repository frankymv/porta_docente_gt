// Livewire (v3/v4) ya incluye Alpine.js automáticamente con @livewireScripts.
// Aquí solo registramos stores y componentes de Alpine.

document.addEventListener('alpine:init', () => {
    // --- Modo oscuro persistente ---
    Alpine.store('theme', {
        dark: false,
        init() {
            const saved = localStorage.getItem('theme');
            this.dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.apply();
        },
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            this.apply();
        },
        apply() {
            document.documentElement.classList.toggle('dark', this.dark);
        },
    });

    // --- Notificaciones (toasts) ---
    // Uso desde Livewire: $this->dispatch('notify', message: 'Guardado');
    Alpine.data('toasts', () => ({
        items: [],
        add(message, type = 'ok') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    }));

    // --- Barra de progreso de lectura ---
    Alpine.data('readingProgress', () => ({
        pct: 0,
        update() {
            const h = document.documentElement;
            const max = h.scrollHeight - h.clientHeight;
            this.pct = max > 0 ? Math.min(100, Math.round((h.scrollTop / max) * 100)) : 0;
        },
    }));

    // --- Copiar enlace ---
    Alpine.data('copyLink', () => ({
        copied: false,
        async copy() {
            await navigator.clipboard.writeText(window.location.href);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        },
    }));
});
