import { ReactNode } from 'react';
import AppHeader from '../general/app-header';

export default function AppLayout({ children }: { children: ReactNode }) {
    return (
        <div>
            <AppHeader />
            <hr />
            {children}
        </div>
    );
}
