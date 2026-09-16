export class GroupState extends EventTarget {
    constructor() {
        super();
        this.breed = '';
        this.label = 'Alle racer';
        this.mode = 'results';
        this.initialized = false;
    }

    configure(breed) {
        if (!this.initialized) { this.breed = breed || ''; this.initialized = true; }
    }

    search(breed, label) {
        this.breed = breed;
        this.label = label;
        this.mode = 'results';
        this.dispatchEvent(new Event('change'));
    }

    toggleFavorites() {
        this.mode = this.mode === 'favorites' ? 'results' : 'favorites';
        this.dispatchEvent(new Event('change'));
    }
}
