import AuthLayout from '@/components/layout/auth-layout';
import ButtonComponent from '@/components/ui/button-component';
import ErrorTextComponent from '@/components/ui/error-text-component';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useCallback } from 'react';

type Form = {
    throttle: string;
};

export default function Page() {
    const { post, clearErrors, processing, errors } = useForm<Required<Form>>({
        throttle: '',
    });

    const handleSubmit = useCallback<FormEventHandler<HTMLFormElement>>(
        (e) => {
            e.preventDefault();
            post(route('verification.resend'), {});
            clearErrors();
        },
        [post, clearErrors],
    );

    return (
        <>
            <Head title="Verification Notice"></Head>
            <AuthLayout>
                <form
                    onSubmit={handleSubmit}
                    className="flex w-full max-w-sm flex-col gap-6 p-4"
                >
                    <div className="flex flex-col items-start">
                        <h2 className="text-xl font-medium">Verification Notice</h2>
                        <p className="py-1 text-sm">Verification email is sent to you</p>
                    </div>

                    {errors.throttle && <ErrorTextComponent children={errors.throttle} />}

                    <ButtonComponent
                        type="submit"
                        disabled={processing}
                    >
                        Resend Email
                    </ButtonComponent>
                </form>
            </AuthLayout>
        </>
    );
}
