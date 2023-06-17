import React from 'react'
import ContentLoader from 'react-content-loader'

export function CardSkeleton({...props}) {
    return (
        <div>
            <ContentLoader
                width={700}
                height={300}
                viewBox="0 0 700 300"
                backgroundColor="#f5f5f5"
                foregroundColor="#dbdbdb"
                {...props}
                title={'Загружается'}
            >
                {/*Название*/}
                <rect x="156" y="95" rx="3" ry="3" width="231" height="25"/>
                {/*Описание*/}
                <rect x="156" y="140" rx="3" ry="3" width="220" height="6"/>
                <rect x="156" y="160" rx="3" ry="3" width="231" height="6"/>
                {/*Тип*/}
                <rect x="156" y="180" rx="8" ry="8" width="102" height="20"/>

                {/*Вертикальные полоски*/}
                <rect x="110" y="70" rx="3" ry="3" width="7" height="160" />
                <rect x="420" y="70" rx="3" ry="3" width="6" height="160" />

                {/*Горизонтальные полоски*/}
                <rect x="110" y="222" rx="3" ry="3" width="315" height="8" />
                <rect x="110" y="68" rx="3" ry="3" width="315" height="8" />
            </ContentLoader>
        </div>
    );
}

export function LiteratureCardSkeleton({...props}) {
    return (
        <div>
            <ContentLoader
                width={700}
                height={300}
                viewBox="0 0 700 300"
                backgroundColor="#f5f5f5"
                foregroundColor="#dbdbdb"
                {...props}
                title={'Загружается'}
            >
                {/*Картинка*/}
                <rect x="110" y="6" rx="16" ry="16" width="316" height="80" />
                {/*Название*/}
                <rect x="156" y="95" rx="3" ry="3" width="231" height="25"/>
                {/*Описание*/}
                <rect x="156" y="140" rx="3" ry="3" width="220" height="6"/>
                <rect x="156" y="160" rx="3" ry="3" width="231" height="6"/>
                {/*Тип*/}
                <rect x="156" y="180" rx="8" ry="8" width="102" height="20"/>

                {/*Перейти*/}
                <rect x="156" y="210" rx="3" ry="3" width="102" height="6"/>

                {/*Вертикальные полоски*/}
                <rect x="110" y="70" rx="3" ry="3" width="7" height="160" />
                <rect x="420" y="70" rx="3" ry="3" width="6" height="160" />

                {/*Горизонтальные полоски*/}
                <rect x="110" y="222" rx="3" ry="3" width="315" height="8" />
                <rect x="110" y="68" rx="3" ry="3" width="315" height="8" />
            </ContentLoader>
        </div>
    );
}
