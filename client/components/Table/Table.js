// import './Table.scss'
// import classNames from 'classnames'
import {DeleteForever, Edit} from '@material-ui/icons'
import React from 'react'
import {useDispatch} from 'react-redux'


import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TablePagination from '@mui/material/TablePagination';
import TableRow from '@mui/material/TableRow';

const ColumnGroupingTable = ({
                                 header,
                                 data,
                                 deleteCallback,
                                 modalOpenCallback,
                                 modalDataCallback,
                                 getDataCallback,
                                 className,
                                 buttons
                             }) => {
    const [page, setPage] = React.useState(0);
    const [rowsPerPage, setRowsPerPage] = React.useState(5);

    const dispatch = useDispatch();

    const handleChangePage = (event, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(+event.target.value);
        setPage(0);
    };

    return (
        <Paper sx={{width: '100%'}}>
            <TableContainer sx={{maxHeight: 640}}>
                <Table
                    stickyHeader
                    aria-label="sticky table"
                >
                    <TableHead>
                        <TableRow>
                            {
                                header.map((item, i) =>
                                    <TableCell
                                        key={i}
                                    >
                                        {item}
                                    </TableCell>)
                            }
                            {
                                buttons && <TableCell/>
                            }
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {
                            data
                                .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                                .map((dataItem, i) =>
                                    <TableRow
                                        hover
                                        role="checkbox"
                                        tabIndex={-1}
                                        key={i}
                                    >
                                        {
                                            Object.entries(dataItem).map(([key, value]) =>
                                                <TableCell key={key}>{value}</TableCell>)
                                        }
                                        {
                                            buttons && <TableCell>
                                                <Edit
                                                    style={{cursor: 'pointer'}}
                                                    onClick={() => {
                                                        modalOpenCallback(true)
                                                        modalDataCallback(dataItem)
                                                    }}
                                                />
                                                <DeleteForever
                                                    style={{cursor: 'pointer'}}
                                                    onClick={async () => {
                                                        await dispatch(deleteCallback(dataItem.id))
                                                        await dispatch(getDataCallback())
                                                    }}
                                                />
                                            </TableCell>
                                        }
                                    </TableRow>)
                        }
                    </TableBody>
                </Table>
            </TableContainer>
            <TablePagination
                rowsPerPageOptions={[5, 10, 25, 100]}
                component="div"
                count={data.length}
                rowsPerPage={rowsPerPage}
                page={page}
                onPageChange={handleChangePage}
                onRowsPerPageChange={handleChangeRowsPerPage}
                labelRowsPerPage="Кол. строк на странице"
            />
        </Paper>
    )
}

export default ColumnGroupingTable