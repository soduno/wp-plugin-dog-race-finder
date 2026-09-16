export class FavoritesButton {
    constructor(root, group, store) {
        this.root = root;
        this.group = group;
        this.store = store;
        root.addEventListener('click', () => {
            group.toggleFavorites();
            const results = [...document.querySelectorAll('[data-drf-component]')].find(element => element.dataset.group === root.dataset.group && ['results', 'favorites'].includes(element.dataset.drfComponent));
            results?.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start' });
        });
        store.addEventListener('change', () => this.render());
        group.addEventListener('change', () => this.render());
        this.render();
    }

    render() {
        this.root.querySelector('[data-drf-count]').textContent = this.store.items.length;
        this.root.setAttribute('aria-pressed', String(this.group.mode === 'favorites'));
    }
}
