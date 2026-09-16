export class ApiClient {
    static pending = new Map();

    constructor(endpoint) { this.endpoint = endpoint; }

    async get(path, parameters = {}) {
        const url = new URL(this.endpoint);
        // WordPress can expose REST routes through a query parameter on plain permalinks.
        if (url.searchParams.has('rest_route')) {
            url.searchParams.set('rest_route', `${url.searchParams.get('rest_route').replace(/\/$/, '')}/${path}`);
        } else {
            url.pathname = `${url.pathname.replace(/\/$/, '')}/${path}`;
        }
        Object.entries(parameters).forEach(([key, value]) => url.searchParams.set(key, value));
        const key = url.href;
        if (!ApiClient.pending.has(key)) {
            ApiClient.pending.set(key, this.request(url).finally(() => ApiClient.pending.delete(key)));
        }
        return ApiClient.pending.get(key);
    }

    async request(url) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 15000);
        try {
            const response = await fetch(url, { signal: controller.signal, credentials: 'omit' });
            const body = await response.json();
            if (!response.ok) throw new Error(body.message || 'Der opstod en fejl. Prøv igen.');
            return body;
        } catch (error) {
            if (error.name === 'AbortError') throw new Error('Det tog for lang tid at hente hundene. Prøv igen.');
            throw error;
        } finally { clearTimeout(timeout); }
    }
}
