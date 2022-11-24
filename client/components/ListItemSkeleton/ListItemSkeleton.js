import React from 'react';
import ContentLoader from "react-content-loader";
import s from './ListItemSkeleton.module.scss';

export default function ListItemSkeleton({proportions, ...props}) {
    return (
        <ContentLoader {...props} className={s.skeleton} viewBox="0 0 1130 64" title={'Загружается'}>
            {
                proportions.map((share, i) => {
                    const reducer = (prev, current) => prev + current;
                    let xStart = 1130 * proportions.slice(0, i).reduce(reducer, 0);
                    xStart = i !== 0 ? xStart + 22 : xStart;

                    return <rect key={i} x={xStart} y="25" rx="7" ry="7" width={1130*share - 72} height="14" />
                })
            }
        </ContentLoader>
    );
}