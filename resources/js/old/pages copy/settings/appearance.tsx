import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/old/built-in/appearance-tabs';
import HeadingSmall from '@/old/built-in/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/old/layouts/app-layout';
import SettingsLayout from '@/old/layouts/settings/layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/settings/appearance',
    },
];

export default function Appearance() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appearance settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
