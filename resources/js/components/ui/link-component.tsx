import { InertiaLinkProps, Link } from '@inertiajs/react';
import { ReactNode } from 'react';

type Props = InertiaLinkProps & { children: ReactNode };

export default function LinkComponent({ children, className, ...props }: Props) {
    return (
        <Link
            className={`cursor-pointer rounded-md bg-black px-4 py-2 text-center text-sm font-medium text-white dark:bg-white dark:text-black ${className}`}
            {...props}
        >
            {children}
        </Link>
    );
}
