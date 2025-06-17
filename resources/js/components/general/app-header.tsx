import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import LinkComponent from '../ui/link-component';
import SwitchMode from './switch-mode';

export default function AppHeader() {
    const { auth } = usePage<SharedData>().props;

    return (
        <header className="flex flex-wrap items-center justify-between gap-2 p-4">
            <Link href={route('home')}>
                <h1 className="text-xl font-semibold tracking-wide">SAFI SIDDIQUI</h1>
            </Link>

            <div className="flex items-center gap-2">
                {auth.user ? (
                    <LinkComponent
                        method="post"
                        as="button"
                        href={route('logout')}
                    >
                        Logout
                    </LinkComponent>
                ) : (
                    <LinkComponent href={route('login.page')}>Login</LinkComponent>
                )}

                <SwitchMode />
            </div>
        </header>
    );
}
