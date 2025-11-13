import { useCallback } from 'react';

export function useMobileNavigation() {
    return useCallback(() => document.body.style.removeProperty('pointer-events'), []); // Remove pointer-events style from body...
}
