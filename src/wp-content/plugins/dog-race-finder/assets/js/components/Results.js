import { ApiClient } from '../core/ApiClient.js';

export class Results {
    constructor(root, group, store, cards) {
        this.root = root;
        this.group = group;
        this.store = store;
        this.cards = cards;
        this.grid = root.querySelector('[data-drf-grid]');
        this.status = root.querySelector('[role=status]');
        this.heading = root.querySelector('[data-drf-heading]');
        this.more = root.querySelector('[data-drf-more]');
        this.refresh = root.querySelector('[data-drf-refresh]');
        this.api = new ApiClient(root.dataset.endpoint);
        this.group.configure(root.dataset.breed);
        this.requestId = 0;
        this.images = [];
        this.more.addEventListener('click', () => this.load(true));
        this.refresh.addEventListener('click', () => this.load(false));
        this.group.addEventListener('change', () => this.renderMode());
        this.store.addEventListener('change', () => {
            if (this.group.mode === 'favorites') this.renderSaved();
            else this.cards.syncHearts(this.root);
        });
        this.renderMode();
    }

    renderMode() {
        this.requestId++;
        if (this.group.mode === 'favorites') this.renderSaved();
        else this.load(false);
    }

    renderSaved() {
        this.root.removeAttribute('aria-busy');
        this.heading.textContent = 'Mine favoritter';
        this.more.hidden = true;
        this.refresh.hidden = true;
        this.grid.replaceChildren(...this.store.items.map(item => this.cards.create(item, true, message => this.report(message))));
        this.report(this.store.items.length ? `${this.store.items.length} gemte billeder.` : 'Du har ikke gemt nogen favoritter endnu. Tryk på hjertet på et billede.');
    }

    report(message) { this.status.textContent = message; }

    async load(append) {
        if (this.group.mode !== 'results') return;
        const requestId = ++this.requestId;
        this.heading.textContent = this.group.breed ? (this.group.label === 'Alle racer' ? this.group.breed.replace('/', ' · ') : this.group.label) : 'Et godt sted at starte';
        this.refresh.hidden = false;
        this.more.hidden = false;
        this.more.disabled = true;
        this.refresh.disabled = true;
        this.root.setAttribute('aria-busy', 'true');
        this.report('Henter hunde…');
        if (!append) this.skeletons();
        try {
            const data = await this.api.get('images', { breed: this.group.breed, count: this.root.dataset.count });
            if (requestId !== this.requestId || this.group.mode !== 'results') return;
            const existing = new Set(append ? this.images.map(item => item.url) : []);
            const fresh = data.images.filter(item => this.store.valid(item) && !existing.has(item.url));
            if (!append) { this.images = []; this.grid.replaceChildren(); }
            this.images.push(...fresh);
            this.grid.append(...fresh.map(item => this.cards.create(item, this.root.dataset.favorites === '1', message => this.report(message))));
            this.report(this.images.length === 0 ? 'Der blev ikke fundet nogen billeder.' : (append && fresh.length === 0 ? 'Du har set hele det aktuelle udvalg. Prøv igen om lidt for at se flere.' : `${this.images.length} hundebilleder · find din favorit`));
        } catch (error) {
            if (requestId !== this.requestId || this.group.mode !== 'results') return;
            if (!append) { this.images = []; this.grid.replaceChildren(); }
            this.report(error.message || 'Billederne kunne ikke hentes. Prøv igen.');
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'drf-text-button';
            retry.textContent = 'Prøv igen';
            retry.addEventListener('click', () => this.load(append));
            this.status.append(' ', retry);
        } finally {
            if (requestId === this.requestId) {
                this.root.removeAttribute('aria-busy');
                this.more.disabled = false;
                this.refresh.disabled = false;
            }
        }
    }

    skeletons() {
        this.grid.replaceChildren();
        for (let index = 0; index < Math.min(Number(this.root.dataset.count), 6); index++) {
            const item = document.createElement('div');
            item.className = 'drf-skeleton';
            item.setAttribute('aria-hidden', 'true');
            this.grid.append(item);
        }
    }
}
