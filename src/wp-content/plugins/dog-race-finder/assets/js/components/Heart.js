export class Heart {
    constructor(root, store, cards) {
        this.item = { url: root.dataset.url, label: root.dataset.label, breed: root.dataset.breed || '' };
        const status = document.createElement('span');
        status.className = 'drf-sr-only';
        status.setAttribute('role', 'status');
        root.after(status);
        root.addEventListener('click', () => {
            const result = store.toggle(this.item);
            status.textContent = result.message || (store.has(this.item.url) ? 'Billedet er gemt som favorit.' : 'Billedet er fjernet fra favoritter.');
        });
        store.addEventListener('change', () => cards.updateHeart(root, this.item));
        cards.updateHeart(root, this.item);
    }
}
