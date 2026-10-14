import { test, beforeEach } from 'node:test';
import assert from 'node:assert/strict';
import { FavoritesStore } from '../../assets/js/core/FavoritesStore.js';
import { GroupState } from '../../assets/js/core/GroupState.js';
import { ApiClient } from '../../assets/js/core/ApiClient.js';

globalThis.window = new EventTarget();
const memory = new Map();
const storage = { getItem: key => memory.get(key) ?? null, setItem: (key, value) => memory.set(key, value) };
globalThis.localStorage = storage;
const imageBase = 'https://images.example.test/';
const item = { url: `${imageBase}breeds/pug/example.jpg`, breed: 'pug', label: 'Pug' };
beforeEach(() => { memory.clear(); globalThis.localStorage = storage; });

test('favorites survive a new store and can be removed', () => {
    const store = new FavoritesStore(imageBase);
    assert.equal(store.toggle(item).ok, true);
    const reloaded = new FavoritesStore(imageBase);
    assert.equal(reloaded.has(item.url), true);
    reloaded.toggle(item);
    assert.deepEqual(reloaded.items, []);
});

test('invalid persisted data and untrusted URLs are rejected', () => {
    memory.set(FavoritesStore.KEY, JSON.stringify([item, item, { ...item, url: 'javascript:alert(1)' }, { ...item, url: 'https://attacker.test/breeds/pug/a.jpg' }]));
    const store = new FavoritesStore(imageBase);
    assert.equal(store.items.length, 1);
    assert.equal(store.toggle({ ...item, url: 'https://attacker.test/a.jpg' }).ok, false);
});

test('storage errors keep favorites usable in memory with an explicit message', () => {
    globalThis.localStorage = { getItem() { throw new Error('denied'); }, setItem() { throw new Error('denied'); } };
    const store = new FavoritesStore(imageBase);
    const result = store.toggle(item);
    assert.equal(store.has(item.url), true);
    assert.match(result.message, /kun/);
});

test('group searches and favorite modes remain independent', () => {
    const first = new GroupState();
    const second = new GroupState();
    first.configure('pug'); second.configure('hound');
    first.search('labrador', 'Labrador');
    first.toggleFavorites();
    assert.equal(second.breed, 'hound');
    assert.equal(second.mode, 'results');
    assert.equal(first.mode, 'favorites');
});

test('REST URLs support plain permalinks and WordPress in a subdirectory', async () => {
    const seen = [];
    const original = globalThis.fetch;
    globalThis.fetch = async url => { seen.push(new URL(url)); return { ok: true, json: async () => ({ images: [] }) }; };
    try {
        await new ApiClient('https://wp.example.test/blog/?rest_route=/dog-race-finder/v1/').get('images', { breed: 'hound/afghan', count: 4 });
        assert.equal(seen[0].searchParams.get('rest_route'), '/dog-race-finder/v1/images');
        assert.equal(seen[0].pathname, '/blog/');
        assert.equal(seen[0].searchParams.get('breed'), 'hound/afghan');
        await new ApiClient('https://wp.example.test/blog/wp-json/dog-race-finder/v1/').get('breeds');
        assert.equal(seen[1].pathname, '/blog/wp-json/dog-race-finder/v1/breeds');
    } finally { globalThis.fetch = original; }
});
