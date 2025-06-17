import AppLayout from '@/components/layout/app-layout';
import LinkComponent from '@/components/ui/link-component';
import { Head } from '@inertiajs/react';
import { useMemo } from 'react';

export default function Welcome() {
    const apps = useMemo(
        () => [
            {
                title: 'Email Verification App',
                description:
                    'This app only opens if email is not verified, if user has already verified email then this app will redirect user to home page.',
                href: route('verification.notice'),
                endColor: 'to-blue-300',
            },
            {
                title: 'Profile App',
                description:
                    'This app allows user to update their personal information and check details linked to other apps but only if user is verified',
                href: route('home'),
                endColor: 'to-green-300',
            },
            {
                title: 'Ecommerce App',
                description:
                    'This app is single vendor ecommerce store with complete interace to run an online store, monitor orders, hanlde payments and more',
                href: route('home'),
                endColor: 'to-yellow-300',
            },
        ],
        [],
    );

    return (
        <>
            <Head title="Welcome"></Head>
            <AppLayout>
                <div className="flex flex-col gap-4 p-4">
                    <div className="flex flex-col items-center gap-1 py-20 text-center">
                        <p className="text-2xl font-semibold">Full Stack React Laravel Developer</p>
                        <p className="text-xl font-medium">Showcasing Mini Apps</p>
                        <p className="">Welcome to my profile</p>
                    </div>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {apps.map(({ title, description, href, endColor }, index) => (
                            <div
                                className="flex flex-col gap-2 rounded-md border p-2"
                                key={index}
                            >
                                <div className={`h-20 rounded-md bg-gradient-to-r from-black ${endColor}`}></div>

                                <div className="flex flex-col gap-1">
                                    <p className="text-lg font-medium">{title}</p>
                                    <p className="tracking-tight">{description}</p>
                                </div>
                                <LinkComponent href={href}>Explore App</LinkComponent>
                            </div>
                        ))}
                    </div>
                </div>
            </AppLayout>
        </>
    );
}
