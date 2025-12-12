import { type InertiaLinkProps } from '@inertiajs/react';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

/**
 * Merges Tailwind class names, resolving any conflicts.
 *
 * @param inputs - An array of class names to merge.
 * @returns A string of merged and optimized class names.
 */
export function cn(...inputs: ClassValue[]): string {
    return twMerge(clsx(inputs));
}

export function isSameUrl(url1: NonNullable<InertiaLinkProps['href']>, url2: NonNullable<InertiaLinkProps['href']>) {
    return resolveUrl(url1) === resolveUrl(url2);
}

export function resolveUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

export function buildParams(overrides: Record<string, string | number | boolean | undefined> = {}) {
    const params: Record<string, string | number | boolean | undefined> = {};

    const urlParams = new URLSearchParams(globalThis.window.location.search);
    for (const [key, value] of urlParams.entries()) {
        params[key] = value;
    }

    for (const k of Object.keys(overrides)) {
        const v = overrides[k];

        if (v === undefined || v === null || v === '') params[k] = undefined;
        else params[k] = v;
    }

    return params;
}

export function normalizeString(str: string) {
    return str
        .toLowerCase()
        .trim()
        .normalize('NFD') // Normalize to decomposed form
        .replaceAll(/[\u0300-\u036f]/g, '') // Remove diacritics
        .replaceAll(/[_\-\s]+/g, ' ') // convert underscores, dashes and repeated whitespace to single space
        .replaceAll(/[^\p{L}\p{N} ]+/gu, '') // Remove non-letter/number characters (keep spaces) - Unicode aware
        .replaceAll(/(^|\s)\p{L}/gu, (match) => match.toUpperCase()); // Capitalize first letter of each word - Unicode aware
}

export function formatCurrency(
    amount: string | number,
    currency: Intl.NumberFormatOptions['currency'] = 'USD',
    locale: Intl.LocalesArgument = 'en-US',
) {
    const parsedAmount = Number.parseFloat(String(amount));

    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency,
    }).format(parsedAmount);
}
