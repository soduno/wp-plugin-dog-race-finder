export class Favorites {
    constructor(root, store, cards) {
        this.root = root;
        this.store = store;
        this.cards = cards;
        this.store.addEventListener('change', () => this.render());
        this.render();
    }

    render() {
        const grid = this.root.querySelector('[data-drf-grid]');
        const status = this.root.querySelector('[role=status]');
        grid.replaceChildren(...this.store.items.map(item => this.cards.create(item, true, message => { status.textContent = message; })));
        status.textContent = this.store.items.length ? `${this.store.items.length} gemte billeder i denne browser.` : 'Du har ikke gemt nogen favoritter endnu. Tryk på et hjerte for at komme i gang.';
    }
}
