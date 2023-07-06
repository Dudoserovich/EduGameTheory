import React, {forwardRef} from 'react';
import Select from 'react-select'
import {RefCallBack} from "react-hook-form";

const customStyles = {
    control: (provided, state) => {
        const borderRadius = state.menuIsOpen ? '6px 6px 0 0' : '6px';
        const height = 'auto';
        const minHeight = '38px';

        return {...provided, borderRadius, height, minHeight}
    },
    valueContainer: (provided) => ({
        ...provided,
        overflowX: 'auto',
        flexWrap: 'none',
        minHeight: '100%'
    }),
    multiValue: (provided) => ({
        ...provided,
        flexShrink: '0'
    }),
    loadingIndicator: (provided) => ({
        ...provided,
        // color: '#DB001B'
    }),
    menu: (provided) => ({
        ...provided,
        borderTopLeftRadius: 0,
        borderTopRightRadius: 0,
        border: '1px solid #1976d2',
        borderTop: 'none',
        boxShadow: '0 0 0 1px #1976d2',
        padding: 0,
        margin: 0
    })
}

const customTheme = (theme) => ({
    ...theme,
    colors: {
        ...theme.colors,
        // primary25: '#F6BFC6',
        // primary: '#DB001B',
    },
})

interface IProps {
    className: string,
    isMulti: boolean,
    inputRef: RefCallBack,
    instanceId: string,
    isClearable: boolean,
    isSearchable: boolean,
    type: string,
    isLoading: boolean
    loadingMessage: () => string,
    placeholder: string,
    noOptionsMessage: () => string,
    autoComplete: string,
    options: any[],
    getOptionLabel: (option) => string | null,
    getOptionValue: (option) => number | null,
    onChange: (option) => void,
    onBlur: () => void
}

const CustomSelect = forwardRef((props: IProps, ref: RefCallBack) => (
    <Select
        {...props}
        ref={ref}
        styles={customStyles}
        theme={theme => customTheme(theme)}/>
));

export default CustomSelect;