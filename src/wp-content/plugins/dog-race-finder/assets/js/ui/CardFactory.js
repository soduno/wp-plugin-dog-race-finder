export class CardFactory {
    constructor(store, lightbox) { this.store = store; this.lightbox = lightbox; }

    heart(item, report = () => {}) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'drf-heart';
        button.dataset.favoriteUrl = item.url;
        this.updateHeart(button, item);
        button.addEventListener('click', () => {
            const result = this.store.toggle(item);
            report(result.message);
        });
        return button;
    }

    updateHeart(button, item) {
        const saved = this.store.has(item.url);
        button.textContent = saved ? '♥' : '♡';
        button.setAttribute('aria-pressed', String(saved));
        button.setAttribute('aria-label', `${saved ? 'Fjern' : 'Gem'} ${item.label} ${saved ? 'fra' : 'som'} favorit`);
    }

    create(item, favorites = true, report = () => {}) {
        const card = document.createElement('article');
        card.className = 'drf-card';
        const photo = document.createElement('div');
        photo.className = 'drf-card-photo';
        const open = document.createElement('button');
        open.type = 'button';
        open.className = 'drf-photo-button';
        open.setAttribute('aria-label', `Se større billede af ${item.label}`);
        open.addEventListener('click', () => this.lightbox.open(item));
        const image = document.createElement('img');
        image.src = item.url;
        image.alt = item.label;
        image.loading = 'lazy';
        image.decoding = 'async';
        image.addEventListener('error', () => {
            image.remove();
            open.textContent = 'Billedet kunne ikke indlæses';
            open.disabled = true;
        }, { once: true });
        open.append(image);
        photo.append(open);
        if (favorites) photo.append(this.heart(item, report));
        const label = document.createElement('h4');
        label.textContent = item.label;
        card.append(photo, label);
        return card;
    }

    syncHearts(root) {
        root.querySelectorAll('[data-favorite-url]').forEach(button => {
            this.updateHeart(button, { url: button.dataset.favoriteUrl, label: button.closest('article')?.querySelector('h4')?.textContent || 'Hund' });
        });
    }
}
