import React, { useState } from 'react';
import s from './CustomTextArea.module.scss';

import { v4 as uuid } from 'uuid';

export default function CustomTextArea({header, value, onChange}) {
    const [competences, setCompetences] = useState(value);

    function insert(index, string, inserValue) {
        if(index < 0) {
            return inserValue + string;
        }

        if(index === string.length) {
            return string + inserValue;
        }

        return string.slice(0, index + 1) + inserValue + string.slice(index + 1);
    }

    function onKeyDownHandler(e) {
        if(e.key === 'Tab') {
            e.preventDefault();
            if(e.shiftKey) {
                return;
            }
            const value = e.target.value;
            const selectionStart = e.target.selectionStart - 1;  
            const selectionEnd = e.target.selectionEnd - 1;  
                    
            e.target.value = insert(value.lastIndexOf('\n', selectionStart), value, '    ');
            e.target.selectionStart = selectionStart + 5;
            e.target.selectionEnd = selectionEnd + 5;
            return;
        }
        
        if(e.key.replace(/\S/, '').length === 0 && e.target.value === '') {
            e.target.value = '- ';
        }

        if(e.key === 'Enter') {
            e.preventDefault();
            let value = e.target.value;
            let selectionStart = e.target.selectionStart;
            const selectionEnd = e.target.selectionEnd;

            const uid = uuid();
            const uidText = `[id](#${uid}) `;

            const regex = /(?<question>^(?<spaces>(\s{4})*)[-*]\s+(\[id\]\(#(?<uid>\S.*?)\))? *)/;
            const startQuestion = (value.lastIndexOf('\n', selectionStart) === -1 ? value.indexOf('') : value.lastIndexOf('\n', selectionStart-1) + 1);
            let endUid = null;
            let prevUidText = null;

            const questionWithoutTextMatch = value.slice(startQuestion).match(regex) ?? null;
            if (questionWithoutTextMatch) {
                let questionWithoutText = questionWithoutTextMatch.groups.question;
                endUid = startQuestion + questionWithoutText.length;

                const prevUid = questionWithoutTextMatch.groups.uid ?? null;
                prevUidText = prevUid ? `[id](#${prevUid}) ` : null;
            }

            const spaces = questionWithoutTextMatch ? (questionWithoutTextMatch.groups.spaces ?? '') : '';

            if (questionWithoutTextMatch && selectionStart >= startQuestion && selectionStart <= endUid) {
                value = value.slice(0, startQuestion) + spaces + `- ${uidText}` + (prevUidText ? '' : '\n') + value.slice(endUid);

                e.target.value = insert(endUid - 1, value, `\n${spaces}- ` + (prevUidText ?? ''));
                e.target.selectionStart = endUid + 2 + spaces.length + (prevUidText ? prevUidText.length + 1: uidText.length + 3);
                e.target.selectionEnd = selectionEnd + 2 + spaces.length + (prevUidText ? prevUidText.length + 1 : uidText.length + 3);
            } else {
                e.target.value = insert(selectionStart - 1, value, `\n${spaces}- ` + uidText);
                e.target.selectionStart = selectionStart + 2 + spaces.length + (uidText.length + 1);
                e.target.selectionEnd = selectionEnd + 2 + spaces.length + (uidText.length + 1);
            }
        }
    }

    function onChangeHandler(e, index) {
        const value = e.target.value;
        let newCompetences = competences.map(item => ({...item}));
        newCompetences[index].competences = value;

        if(!(JSON.stringify(competences[index]) === JSON.stringify(newCompetences[index]))) {
            setCompetences(newCompetences);
            onChange(newCompetences);
        }
    }

    function emptyFieldFocus(e) {
        const value = e.target.value;
        const selectionStart = e.target.selectionStart;
        const selectionEnd = e.target.selectionEnd;

        const uid = uuid();
        const uidText = `[id](#${uid}) `;

        e.target.value = (value === "" ? insert(selectionStart, value, '- ' + uidText) : value);
        e.target.selectionStart = selectionStart + 3 + (uidText.length + 1);
        e.target.selectionEnd = selectionEnd + 3 + (uidText.length + 1);
    }

    return (
        <div className={s.content}>
            <h2 className={s.h2}>{`Специализация: ${header.specialty?.name}. Категория: ${header.category?.name}`}</h2>
            {
                competences?.map((element, i) =>
                    <>
                        <h3 key={`title-${i}`} className={s.h3} grade-id={element.id}>{`${element.name} - ${element.lvl}`}</h3>
                        <textarea
                            className={s.text} 
                            key={`textarea-${i}`}
                            type={'text'}
                            defaultValue={element.competences?.substring(0, element.competences?.length)}
                            onKeyDown={event => {onKeyDownHandler(event); onChangeHandler(event, i)}}
                            onChange={event => onChangeHandler(event, i)}
                            onFocus={event => emptyFieldFocus(event)}
                        />
                    </>
                )
            } 
        </div>
    );
}