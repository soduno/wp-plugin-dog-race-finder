import { FavoritesStore } from './core/FavoritesStore.js';
import { GroupState } from './core/GroupState.js';
import { Lightbox } from './ui/Lightbox.js';
import { CardFactory } from './ui/CardFactory.js';
import { Search } from './components/Search.js';
import { Results } from './components/Results.js';
import { Favorites } from './components/Favorites.js';
import { FavoritesButton } from './components/FavoritesButton.js';
import { Heart } from './components/Heart.js';

class GalleryApplication {
    constructor() {
        this.groups = new Map();
        this.instances = [];
    }

    start() {
        const roots = document.querySelectorAll('[data-drf-component]');
        if (!roots.length) return;
        const store = new FavoritesStore(window.drfConfig.imageBaseUrl);
        const cards = new CardFactory(store, new Lightbox());
        roots.forEach(root => {
            const name = root.dataset.group || 'dogs';
            if (!this.groups.has(name)) this.groups.set(name, new GroupState());
        });
        // Results own the initial breed, independently of where controls appear in the DOM.
        [...roots].filter(root => root.dataset.drfComponent === 'results').forEach(root => {
            this.groups.get(root.dataset.group || 'dogs').configure(root.dataset.breed);
        });
        roots.forEach(root => {
            const name = root.dataset.group || 'dogs';
            if (!this.groups.has(name)) this.groups.set(name, new GroupState());
            const group = this.groups.get(name);
            switch (root.dataset.drfComponent) {
                case 'search': this.instances.push(new Search(root, group)); break;
                case 'results': this.instances.push(new Results(root, group, store, cards)); break;
                case 'favorites': this.instances.push(new Favorites(root, store, cards)); break;
                case 'favorites_button': this.instances.push(new FavoritesButton(root, group, store)); break;
                case 'heart': this.instances.push(new Heart(root, store, cards)); break;
            }
        });
    }
}

new GalleryApplication().start();
