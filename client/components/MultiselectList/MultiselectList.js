import React, { useEffect, useRef, useState } from 'react';
import Checkbox from '../Checkbox/Checkbox';
import s from './MultiselectList.module.scss';
import AutosizeInput from 'react-input-autosize';
import { AiOutlineDelete } from "react-icons/ai";
import Spinner from '../Spinner/Spinner';
import { useWindowSize } from '../../scripts/hooks/useWindowSize';
import classNames from 'classnames';

export default function MultiselectList({items, getLabel = (item) => { return item }, isCheckBox, isLoading, ...props}) {
    const size = useWindowSize();
    const [inputValue, setInputValue] = useState('');
    const [selectedItems, setSelectedItems] = useState([]);
    const textInput = useRef(null);

    const getFilteredItems = (items) =>
        items.filter(
        (item) =>
            selectedItems.indexOf(getLabel(item)) < 0 &&
            getLabel(item).toLowerCase().includes(inputValue.toLowerCase()),
        )

    function selectAllClick() {
        if(selectedItems.length !== items.length) {
            setSelectedItems(items);
        } else {
            removeAllSelectedItems();
        }
    }

    function leftSideItemClick(item) {
        if (selectedItems.indexOf(item) < 0) {
            setSelectedItems([].concat(selectedItems, [item]));
            setInputValue('');
        } else {
            removeSelectedItem(item);
        }
    }

    function removeSelectedItem(selectedItem) {
        let selectedItemIndex = selectedItems.indexOf(selectedItem);
        if (selectedItemIndex >= 0) {
        let newSelectedItemsList = [
            ...selectedItems.slice(0, selectedItemIndex),
            ...selectedItems.slice(selectedItemIndex + 1),
        ]

        setSelectedItems(newSelectedItemsList);
        }
    }

    function removeAllSelectedItems() {
        setSelectedItems([]);
    }

    function valueContainerClick() {
        textInput.current.focus();
    }

    return (
        <div className={s.ctn}>
            {
                isLoading
                ?
                <Spinner/>
                :
                <>
                    <div className={s.column}>
                    <div className={s.value_container} onClick={valueContainerClick}>
                        {inputValue.length === 0 ? <div className={s.placeholder}>Поиск</div> : <></>}
                        <div className={s.input}>
                        <AutosizeInput
                            ref={textInput}
                            name="input-field-name"
                            value={inputValue}
                            onChange={event => { setInputValue(event.target.value) }}/>
                        </div>
                        
                    </div>
                    <div className={s.item__with_border} onClick={() => selectAllClick()}>
                        <Checkbox isCheck={selectedItems.length === items.length}/>
                        <span className={s.item_content}>Выбрать все</span>
                    </div>
                    <div className={s.scroll_container}>
                        <ul {...props} className={s.items__list}>
                        {getFilteredItems(items).map((item, i) => 
                                <li className={s.item} key={i} onClick={() => leftSideItemClick(item)}>
                                    {
                                        isCheckBox
                                        &&
                                        <Checkbox name={i} isCheck={selectedItems.indexOf(item) >= 0} onChange={() => leftSideItemClick(item)}/>
                                    }
                                    <span className={s.item_content}>{getLabel(item)}</span>
                                </li>
                            )
                        }
                        </ul>
                    </div>
                    </div>
                    <div className={classNames(s.column, { [s.hide]: size === 'xs' })}>
                    <div className={s.selected_header__container}>
                        <div className={s.selected_counter}>{selectedItems.length === 0 ? 'Ничего не выбрано' : `Выбрано ${selectedItems.length}`}</div>
                        {
                        selectedItems.length > 1
                        ? <span className={s.clear_all} onClick={() => {removeAllSelectedItems()}}>Удалить все</span>
                        : <></>
                        }
                    </div>
                    <div className={s.scroll_container}>
                        <ul {...props} className={s.items__list}>
                        {
                            selectedItems.map((selectedItem, i) => 
                                <li className={s.item} key={i} onClick={e => {removeSelectedItem(selectedItem)}}>
                                    <span className={s.item_content}>{getLabel(selectedItem)}</span>
                                    <AiOutlineDelete className={s.delete} onClick={e => {removeSelectedItem(selectedItem)}}/>
                                </li>
                            )
                        }
                        </ul>
                    </div>
                    </div>
                </>
            }
        </div>
    );
}