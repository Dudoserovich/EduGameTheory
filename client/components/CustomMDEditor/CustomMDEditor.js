import MDEditor from "@uiw/react-md-editor";
import katex from "katex";
import {getCodeString} from "rehype-rewrite";
import React from "react";

import 'katex/dist/katex.min.css';

export default function CustomMDEditor({value, setValue}) {
    return (
        <MDEditor
            value={value}
            onChange={setValue}
            previewOptions={{
                components: {
                    code: ({inline, children = [], className, ...props}) => {
                        const txt = children[0] || '';
                        console.log(txt);
                        if (inline) {
                            if (typeof txt === 'string' && /^\$\$(.*)\$\$/.test(txt)) {
                                const html = katex.renderToString(txt.replace(/^\$\$(.*)\$\$/, '$1'), {
                                    throwOnError: false,
                                });
                                return <code dangerouslySetInnerHTML={{__html: html}}/>;
                            }
                            return <code>{txt}</code>;
                        }
                        const code = props.node && props.node.children ? getCodeString(props.node.children) : txt;
                        if (
                            typeof code === 'string' &&
                            typeof className === 'string' &&
                            /^language-katex/.test(className.toLocaleLowerCase())
                        ) {
                            const html = katex.renderToString(code, {
                                throwOnError: false,
                            });
                            return <code style={{fontSize: '150%'}} dangerouslySetInnerHTML={{__html: html}}/>;
                        }
                        return <code className={String(className)}>{txt}</code>;
                    },
                },
            }}
        />
    );
}