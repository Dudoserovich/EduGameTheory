import React, {FC, useState} from "react"
import s from "./Rating.module.scss"
import {changeRating} from "../../store/slices/ratingSlice"
import {useAppDispatch} from "../../scripts/hooks/redux";


interface IProps {
    attestationIsOpen: boolean,
    attestedMode: boolean,
    questionId: string,
    attestationId: number,
    answerRating: number
}

const Rating: FC<IProps> = ({attestationIsOpen, attestedMode, questionId, attestationId, answerRating}) => {
    const [rating, setRating] = useState(answerRating)
    const dispatch = useAppDispatch()
    return (
        <div>
            <div className={s.ratingNumber}>Оценка знания: {rating}</div>
            {
                attestationIsOpen && attestedMode &&
                    <input className={s.rating}
                         value={rating}
                         onChange={e => setRating(e.target.valueAsNumber)}
                         onMouseUp={() => dispatch(changeRating({
                             attestationId,
                             question_id: questionId,
                             answer_rating: rating
                         }))}
                         type="range"
                         min="0" max="10"/>
            }
        </div>
    )
}

export default Rating;