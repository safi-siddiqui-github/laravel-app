import { DetailedHTMLProps, LabelHTMLAttributes } from 'react';

type Props = DetailedHTMLProps<LabelHTMLAttributes<HTMLLabelElement>, HTMLLabelElement>;

export default function LabelComponent({ children, ...props }: Props) {
    return (
        <label
            className="text-sm"
            {...props}
        >
            {children}
        </label>
    );
}
