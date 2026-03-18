const baseUrl =
    (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(
        /\/$/,
        '',
    );

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

export async function getJson<T>(path: string): Promise<T> {
    const response = await fetch(toUrl(path), {
        headers: {
            Accept: 'application/json',
        },
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
        headers: {
            Accept: 'application/json',
        },
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
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
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
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
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
