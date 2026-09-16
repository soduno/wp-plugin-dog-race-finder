export class FavoritesStore extends EventTarget {
    static KEY = 'dog-race-finder:favorites:v1';
    static LIMIT = 200;

    constructor(imageBaseUrl) {
        super();
        this.base = new URL(imageBaseUrl);
        this.persistent = true;
        this.items = this.read();
        window.addEventListener('storage', event => {
            if (event.key === FavoritesStore.KEY || event.key === null) {
                this.items = this.read();
                this.dispatchEvent(new Event('change'));
            }
        });
    }

    valid(item) {
        if (!item || typeof item.url !== 'string' || typeof item.label !== 'string' || typeof item.breed !== 'string') return false;
        try {
            const url = new URL(item.url);
            return url.origin === this.base.origin && url.pathname.startsWith(`${this.base.pathname}breeds/`) && url.protocol === 'https:' && !url.username && !url.password;
        } catch { return false; }
    }

    read() {
        try {
            const data = JSON.parse(localStorage.getItem(FavoritesStore.KEY) || '[]');
            if (!Array.isArray(data)) return [];
            const unique = new Map();
            data.filter(item => this.valid(item)).slice(0, FavoritesStore.LIMIT).forEach(item => unique.set(item.url, {
                url: item.url, breed: item.breed.slice(0, 100), label: item.label.slice(0, 100),
            }));
            return [...unique.values()];
        } catch { this.persistent = false; return []; }
    }

    has(url) { return this.items.some(item => item.url === url); }

    toggle(item) {
        if (!this.valid(item)) return { ok: false, message: 'Dette billede kan ikke gemmes.' };
        if (this.has(item.url)) {
            this.items = this.items.filter(saved => saved.url !== item.url);
        } else {
            if (this.items.length >= FavoritesStore.LIMIT) return { ok: false, message: 'Du kan gemme op til 200 favoritter. Fjern en favorit først.' };
            this.items = [...this.items, { url: item.url, breed: item.breed, label: item.label }];
        }
        try {
            localStorage.setItem(FavoritesStore.KEY, JSON.stringify(this.items));
            this.persistent = true;
        } catch { this.persistent = false; }
        this.dispatchEvent(new Event('change'));
        return { ok: true, message: this.persistent ? '' : 'Browseren tillader ikke lagring. Favoritter bevares kun, mens denne side er åben.' };
    }
}
