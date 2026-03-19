const baseUrl =
    (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(
        /\/$/,
        '',
    );

const AUTH_TOKEN_KEY = 'inventory_api_token';

export function getAuthToken(): string | null {
    return localStorage.getItem(AUTH_TOKEN_KEY);
}

export function setAuthToken(token: string): void {
    const normalizedToken = token.trim();

    if (!normalizedToken) {
        localStorage.removeItem(AUTH_TOKEN_KEY);

        return;
    }

    localStorage.setItem(AUTH_TOKEN_KEY, normalizedToken);
}

export function clearAuthToken(): void {
    localStorage.removeItem(AUTH_TOKEN_KEY);
}

const toUrl = (path: string): string => {
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;

    return `${baseUrl}${normalizedPath}`;
};

const toErrorMessage = (status: number, statusText: string, body: string): string => {
    if (!body) {
        return `Request failed (${status}): ${statusText}`;
    }

    try {
        const parsed = JSON.parse(body) as { message?: string; error?: string };

        return parsed.message || parsed.error || `Request failed (${status}): ${body}`;
    } catch {
        return `Request failed (${status}): ${body}`;
    }
};

const createHeaders = (includeJsonBody = false): HeadersInit => {
    const headers: Record<string, string> = {
        Accept: 'application/json',
    };

    if (includeJsonBody) {
        headers['Content-Type'] = 'application/json';
    }

    const token = getAuthToken();

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    return headers;
};

export async function getJson<T>(path: string): Promise<T> {
    const response = await fetch(toUrl(path), {
        headers: createHeaders(),
    });

    if (!response.ok) {
        const body = await response.text();

        throw new Error(toErrorMessage(response.status, response.statusText, body));
    }

    return (await response.json()) as T;
} 

export async function deleteRequest(path: string): Promise<void> {
    const response = await fetch(toUrl(path), {
        method: 'DELETE',
        headers: createHeaders(),
    });

    if (!response.ok) {
        const body = await response.text();

        throw new Error(toErrorMessage(response.status, response.statusText, body));
    }
}  

export async function putJson<T, TBody extends object>(
    path: string,
    body: TBody,
): Promise<T> {
    const response = await fetch(toUrl(path), {
        method: 'PUT',
        headers: createHeaders(true),
        body: JSON.stringify(body),
    });
     
    if (!response.ok) {
        const responseBody = await response.text(); 
        
        throw new Error(toErrorMessage(response.status, response.statusText, responseBody));
    }

    return (await response.json()) as T;
} 

export async function postJson<T, TBody extends object>(
    path: string,
    body: TBody,
): Promise<T> {
    const response = await fetch(toUrl(path), {
        method: 'POST',
        headers: createHeaders(true),
        body: JSON.stringify(body),
    });

    if (!response.ok) {
        const responseBody = await response.text();

        throw new Error(
            toErrorMessage(response.status, response.statusText, responseBody),
        );
    }

    return (await response.json()) as T;
}

export function getApiBaseUrl(): string {
    return baseUrl;
}
