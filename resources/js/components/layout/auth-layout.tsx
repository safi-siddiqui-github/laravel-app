import { ReactNode } from 'react';

export default function AuthLayout({ children }: { children: ReactNode }) {
    return <div className="flex h-screen min-h-fit items-center justify-center">{children}</div>;
}
