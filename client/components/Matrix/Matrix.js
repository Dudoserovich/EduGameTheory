import React from 'react'
import s from "./Matrix.module.scss";

export default function Matrix({task}) {
    console.log(task)

    return (
        <table className={s.backgroundMatrix}>
            <tbody>
            <tr>
                {
                    (task?.name_first_player !== null && task?.name_second_player !== null) ?

                        (<td>{task?.name_first_player} /<br/>{task?.name_second_player}</td>)
                        : (<td>1-ый игрок /<br/>2-ой игрок</td>)
                }
                {
                    (task?.name_first_strategies !== null) ?
                        task?.name_first_strategies.map((strategies, index) => (

                            (<td key={index} className={s.col}>{strategies}</td>)
                        ))
                        :
                        task?.matrix[0].map((strategies, index) => (
                            <td key={index} className={s.col}>{index + 1}-ая стратегия</td>

                        ))
                }
            </tr>
            {task?.matrix.map((row, rowIndex) => (
                <tr key={rowIndex}>
                    {/* Дополнительная ячейка слева с подписью для каждой строки */}
                    {
                        (task?.name_second_strategies !== null) ?
                            (<td key={rowIndex} className={s.col}>{task?.name_second_strategies[rowIndex]}</td>)
                            :
                            <td key={rowIndex} className={s.col}>{rowIndex + 1}-ая стратегия</td>
                    }
                    {row.map((cell, cellIndex) => (
                        <td key={cellIndex} className={s.col}>{cell}</td>
                    ))}
                </tr>
            ))}
            </tbody>
        </table>
    );
}