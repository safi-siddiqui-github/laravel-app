import { DetailedHTMLProps, InputHTMLAttributes } from 'react';

type Props = DetailedHTMLProps<InputHTMLAttributes<HTMLInputElement>, HTMLInputElement>;

export default function InputComponent({ ...props }: Props) {
    return (
        <input
            className="rounded border p-2"
            {...props}
        />
    );
}
