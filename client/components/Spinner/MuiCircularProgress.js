import {Box, CircularProgress} from "@mui/material";
import React from "react";

export default function MuiCircularProgress() {
    return (
        <Box sx={{
            display: 'flex',
            justifyContent: "center",
            flexDirection: "column",
            alignItems: "center"
        }}>
            <CircularProgress/>
        </Box>
    );
}