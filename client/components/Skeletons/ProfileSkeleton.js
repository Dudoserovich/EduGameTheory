import React from 'react';
import ContentLoader from "react-content-loader";

export function ProfileHeaderSkeleton({...props}) {
    return (
        <ContentLoader {...props} viewBox="0 0 1148 86" title={'Загружается'}>
            <circle cx="43" cy="43" r="43"/>
            <rect x="102" y="0" rx="22" ry="22" width="450" height="42" />
            <rect x="102" y="62" rx="9" ry="9" width="220" height="18" />
        </ContentLoader>
    );
}

export function FormSkeleton({...props}) {
    return (
        <ContentLoader {...props} viewBox="0 0 384 394" title={'Загружается'}>
            <rect x="32" y="53" rx="6" ry="6" width="320" height="38" />
            <rect x="32" y="144" rx="6" ry="6" width="320" height="38" />
            <rect x="32" y="235" rx="6" ry="6" width="320" height="38" />
        </ContentLoader>
    );
}

export function VerticalMenuSkeleton({...props}) {
    return (
        <ContentLoader {...props} viewBox="0 0 240 400" title={'Загружается'}>
            <rect x="0" y="0" rx="1" ry="1" width="2" height="400" />
            <rect x="45" y="18" rx="6" ry="6" width="150" height="12" />
            <rect x="32" y="67" rx="6" ry="6" width="120" height="12" />
            <rect x="32" y="116" rx="6" ry="6" width="134" height="12" />
            <rect x="32" y="116" rx="6" ry="6" width="125" height="12" />
            <rect x="32" y="165" rx="6" ry="6" width="133" height="12" />
            <rect x="32" y="214" rx="6" ry="6" width="150" height="12" />
        </ContentLoader>
    );
}