import React from 'react';
import ContentLoader from "react-content-loader";
import s from './CardSkeleton.module.scss';

export default function CardSkeleton({...props}) {
    return (
        <ContentLoader {...props} className={s.skeleton} viewBox="0 0 220 280" title={'Загружается'}>
            <rect x="0" y="0" rx="3" ry="3" width="6" height="280" />
            <rect x="0" y="0" rx="3" ry="3" width="220" height="6" />
            <rect x="214" y="0" rx="3" ry="3" width="6" height="280" />
            <rect x="0" y="274" rx="3" ry="3" width="220" height="6" />

            <rect x="20" y="25" rx="11" ry="11" width="180" height="22" />
            <rect x="52" y="62" rx="9" ry="9" width="116" height="16" />
            <rect x="30" y="100" rx="7" ry="7" width="160" height="12" />
            <rect x="30" y="130" rx="7" ry="7" width="160" height="12" />
            <rect x="30" y="160" rx="7" ry="7" width="160" height="12" />
            <rect x="30" y="190" rx="7" ry="7" width="160" height="12" />
            <rect x="46" y="222" rx="8" ry="8" width="128" height="36" />
        </ContentLoader>
    );
}