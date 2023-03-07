import React, {useEffect, useState} from 'react'
import Link from '../../polyfills/link'
import s from './List.module.scss'
import {RiEmotionSadLine} from 'react-icons/ri'
import ListItemSkeleton from '../ListItemSkeleton/ListItemSkeleton'
import classNames from 'classnames'

export default function List({
                                 className,
                                 header,
                                 items,
                                 isLoading,
                                 proportions,
                                 minimums,
                                 onSelect,
                                 onItemClick,
                                 linked,
                                 linkPattern,
                                 buttons
                             }) {

    const [selectedItems, setSelectedItems] = useState([])

    const cssProportions = proportions.map((share) => {
        return proportions.length * share + 'fr'
    })

    const itemsKeys = []

    {
        for (let key in items[0]) {
            itemsKeys.push(key)
        }
    }

    function comparisonObj(obj1, obj2) {
        return JSON.stringify(obj1) === JSON.stringify(obj2)
    }

    // function onClickHandler(clickedItem) {
    //     let elementIndex = selectedItems.findIndex(item => comparisonObj(item, clickedItem));

    //     if (elementIndex === -1) {
    //         setSelectedItems(selectedItems => selectedItems.concat([clickedItem]));
    //     } else {
    //         setSelectedItems(selectedItems => {
    //             let copy = selectedItems.slice();
    //             copy.splice(elementIndex, 1);
    //             return copy;
    //         });
    //     }
    // }

    // useEffect(() => {
    //     onSelect && onSelect(selectedItems)
    // }, [selectedItems])

    return (
        <div className={classNames(s.content, className)}>
            <div className={s.head} style={{gridTemplateColumns: cssProportions.join(' ')}}>
                {header.map((name, i) =>
                    <span className={s.name} key={i} style={{minWidth: minimums[i]}}
                          title={name}>{name}</span>
                )}
            </div>
            <div className={s.body}>
                {
                    isLoading
                        ?
                        <>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                            <ListItemSkeleton proportions={proportions}/>
                        </>
                        :
                        items.length === 0
                            ?
                            <div className={s.empty}>
                                <RiEmotionSadLine className={s.empty__icon}/>
                                <span className={s.empty__text}>Ничего нет</span>
                            </div>
                            :
                            items.map((item, i) =>
                                linked
                                    ? <Link key={i} href={linkPattern + i}>
                                        <div
                                            className={classNames(s.li, {[s.li_selected]: selectedItems.findIndex(el => comparisonObj(el, item)) !== -1})}
                                            key={i}
                                            style={{gridTemplateColumns: cssProportions.join(' ')}}
                                            onClick={() => {
                                                onItemClick && onItemClick(i)
                                            }}>
                                            {
                                                itemsKeys.map((itemKey, j) =>
                                                    <span className={s.item} title={item[itemKey]}
                                                          key={`${i}-${j}`}
                                                          style={{minWidth: minimums[j]}}
                                                    >{item[itemKey]}</span>
                                                )
                                            }
                                        </div>
                                    </Link>
                                    : <div
                                        className={classNames(s.li, {[s.li_selected]: selectedItems.findIndex(el => comparisonObj(el, item)) !== -1})}
                                        key={i}
                                        style={{
                                            gridTemplateColumns: cssProportions.join(' '),
                                            display: 'flex'
                                        }}
                                        onClick={() => {
                                            onItemClick && onItemClick(i)
                                        }}
                                    >
                                        {
                                            itemsKeys.map((itemKey, j) =>
                                                <span className={s.item}
                                                      title={item[itemKey]}
                                                      key={`${i}-${j}`}
                                                      style={{minWidth: minimums[j]}}
                                                >{item[itemKey]}</span>
                                            )
                                        }
                                        {
                                            buttons && buttons.map((button, i) =>
                                                <div
                                                    key={i}
                                                    style={{marginRight: 10}}
                                                >{button}</div>)
                                        }
                                    </div>
                            )
                }
            </div>
        </div>
    )
}