import React, { forwardRef } from 'react';
import classNames from 'classnames';
import s from './Input.module.scss';
import {Box, TextField} from "@mui/material";

const Input = forwardRef((props, ref) => (
    <Box
        sx={{
            '& .MuiTextField-root': { m: 1, width: '25ch' },
        }}
    >
        <TextField
            id={props.id}
            label={props.label}
            placeholder="Placeholder"
            {...props}
            ref={ref}
        />
    </Box>

));

export default Input;