import './Table.scss'
import classNames from 'classnames'
import {DeleteForever, Edit} from '@material-ui/icons'
import React from 'react'
import {useDispatch} from 'react-redux'

const Table = ({
                   header,
                   data,
                   deleteCallback,
                   modalOpenCallback,
                   modalDataCallback,
                   getDataCallback,
                   className,
                   buttons
               }) => {

    const dispatch = useDispatch()

    return (
        <table className={classNames(className)}>
            <thead>
            <tr>
                {
                    header.map((item, i) =>
                        <th key={i}>{item}</th>)
                }
                {
                    buttons && <th></th>
                }
            </tr>
            </thead>
            <tbody>
            {
                data.map((dataItem, i) =>
                    <tr key={i}>
                        {
                            Object.entries(dataItem).map(([key, value]) =>
                                <td key={key}>{value}</td>)
                        }
                        {
                            buttons && <td>
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
                            </td>
                        }
                    </tr>)
            }
            </tbody>
        </table>
    )
}

export default Table