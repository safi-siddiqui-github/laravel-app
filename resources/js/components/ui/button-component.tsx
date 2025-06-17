import { cn } from '@/lib/utils';
import { Loader2 } from 'lucide-react';
import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';

type Props = DetailedHTMLProps<ButtonHTMLAttributes<HTMLButtonElement>, HTMLButtonElement>;

export default function ButtonComponent({ children, disabled, ...props }: Props) {
    return (
        <button
            className={cn(
                'flex cursor-pointer items-center justify-center rounded-md bg-black px-4 py-2 text-center text-sm font-medium text-white dark:bg-white dark:text-black',
                {
                    'cursor-progress': disabled,
                },
            )}
            {...props}
        >
            {disabled ? <Loader2 className="size-5 animate-spin" /> : children}
        </button>
    );
}
