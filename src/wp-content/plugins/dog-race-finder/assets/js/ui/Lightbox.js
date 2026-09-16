export class Lightbox {
    constructor() {
        this.dialog = document.createElement('dialog');
        this.dialog.className = 'drf drf-lightbox';
        this.dialog.setAttribute('aria-label', 'Stort hundebillede');
        this.closeButton = document.createElement('button');
        this.closeButton.type = 'button';
        this.closeButton.className = 'drf-lightbox-close';
        this.closeButton.textContent = 'Luk ×';
        this.image = document.createElement('img');
        this.caption = document.createElement('p');
        this.dialog.append(this.closeButton, this.image, this.caption);
        document.body.append(this.dialog);
        this.closeButton.addEventListener('click', () => this.dialog.close());
        this.dialog.addEventListener('click', event => { if (event.target === this.dialog) this.dialog.close(); });
    }

    open(item) {
        this.image.src = item.url;
        this.image.alt = item.label;
        this.caption.textContent = item.label;
        this.dialog.showModal();
    }
}
