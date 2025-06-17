import { DetailedHTMLProps, HTMLAttributes } from 'react';

type Props = DetailedHTMLProps<HTMLAttributes<HTMLParagraphElement>, HTMLParagraphElement>;

export default function ErrorTextComponent({ children, className, ...props }: Props) {
    return (
        <p
            className={`text-sm text-red-500 ${className}`}
            {...props}
        >
            {children}
        </p>
    );
}
