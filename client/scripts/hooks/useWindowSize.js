import React, { useEffect, useState } from 'react';

export const useWindowSize = () => {
    const [windowSize, setWindowSize] = useState({
        width: undefined,
        height: undefined,
    });

    useEffect(() => {
        function handleResize() {
            setWindowSize({
                width: window.innerWidth,
                height: window.innerHeight,
            });
        }

        window.addEventListener('resize', handleResize);
        handleResize();
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    if (windowSize.width >= 1550) {
        return 'xl';
    } else if (windowSize.width < 1550 && windowSize.width >= 1270) {
        return 'lg';
    } else if (windowSize.width < 1270 && windowSize.width >= 1000) {
        return 'md';
    } else if (windowSize.width < 1000 && windowSize.width >= 735) {
        return 'sm';
    } else if (windowSize.width < 735) {
        return 'xs';
    }
};