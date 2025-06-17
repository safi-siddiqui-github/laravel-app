import AppLayout from '@/components/layout/app-layout';
import { Head } from '@inertiajs/react';

export default function Welcome() {
    // const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome"></Head>
            <AppLayout>
                <div className="flex flex-col">Home</div>
            </AppLayout>
        </>
    );
}
