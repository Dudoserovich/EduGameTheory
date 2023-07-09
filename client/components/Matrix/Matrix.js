import React from 'react'
import s from "./Matrix.module.scss";

export default function Matrix({matrix = [], style}) {

    return (
        <div style={{maxWidth: "fit-content", ...style}}>
            <table className={s.backgroundMatrix}>
                <tbody>
                {matrix.map((row, rowIndex) => (
                    <tr key={rowIndex}>
                        {row.map((cell, cellIndex) => (
                            <td key={cellIndex} className={s.col}>{cell}</td>
                        ))}
                        <td> - {rowIndex + 1}-ая стратегия</td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    )
}