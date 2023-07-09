import classNames from "classnames";
import s from './Markdown.module.scss';
import React from "react";
import ReactMarkdown from "react-markdown";
import remarkGfm from 'remark-gfm';
import SyntaxHighlighter from 'react-syntax-highlighter';
import { githubGist } from "react-syntax-highlighter/dist/cjs/styles/hljs";
import remarkMath from 'remark-math';
import rehypeKatex from 'rehype-katex';
import rehypeRaw from "rehype-raw";

import 'katex/dist/katex.min.css';

export default function Markdown({className, value, style}) {
    return (
        <div className={classNames(s.markdown_body, className)} style={style}>
            <ReactMarkdown
                children={value}
                remarkPlugins={[remarkGfm, remarkMath]}
                rehypePlugins={[rehypeRaw, rehypeKatex]}
                components={{
                code({node, inline, className, children, ...props}) {
                    const match = /language-(\w+)/.exec(className || '')
                    return !inline && match ? (
                    <SyntaxHighlighter
                        children={String(children)}
                        style={githubGist}
                        language={match[1]}
                        PreTag={'code'}
                        {...props}
                    />
                    ) : (
                    <code className={className} {...props}>
                        {children}
                    </code>
                    )
                }
                }}
            />
        </div>
    );
}