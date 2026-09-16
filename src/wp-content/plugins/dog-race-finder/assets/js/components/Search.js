import { ApiClient } from '../core/ApiClient.js';

export class Search {
    constructor(root, group) {
        this.root = root;
        this.group = group;
        this.select = root.querySelector('[data-drf-breed]');
        this.query = root.querySelector('[data-drf-query]');
        this.button = root.querySelector('button[type=submit]');
        this.status = root.querySelector('[role=status]');
        this.api = new ApiClient(root.dataset.endpoint);
        this.group.configure(root.dataset.breed);
        this.query.addEventListener('input', () => this.options());
        this.root.addEventListener('submit', event => {
            event.preventDefault();
            if (this.select.disabled || this.button.disabled) return;
            this.group.search(this.select.value, this.select.selectedOptions[0]?.textContent || 'Alle racer');
        });
        this.group.addEventListener('change', () => {
            this.query.value = '';
            this.options();
        });
        this.load();
    }

    async load() {
        try {
            const data = await this.api.get('breeds');
            this.breeds = Object.entries(data.breeds);
            this.options();
            this.status.textContent = '';
        } catch {
            this.status.textContent = 'Racerne kunne ikke hentes. ';
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'drf-text-button';
            retry.textContent = 'Prøv igen';
            retry.addEventListener('click', () => this.load());
            this.status.append(retry);
        }
    }

    options() {
        if (!this.breeds) return;
        const query = this.query.value.trim().toLocaleLowerCase();
        const matching = this.breeds.filter(([, label]) => label.toLocaleLowerCase().includes(query));
        this.select.replaceChildren();
        if (!query) this.select.add(new Option('Alle racer', ''));
        matching.forEach(([value, label]) => this.select.add(new Option(label, value)));
        if (!query && [...this.select.options].some(option => option.value === this.group.breed)) this.select.value = this.group.breed;
        this.select.disabled = matching.length === 0;
        this.button.disabled = matching.length === 0;
        this.status.textContent = matching.length === 0 ? 'Ingen racer matcher din søgning.' : '';
    }
}
