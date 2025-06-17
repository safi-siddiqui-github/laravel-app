import AuthLayout from '@/components/layout/auth-layout';
import ButtonComponent from '@/components/ui/button-component';
import ErrorTextComponent from '@/components/ui/error-text-component';
import InputComponent from '@/components/ui/input-component';
import LabelComponent from '@/components/ui/label-component';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useCallback } from 'react';

type Form = {
    email: string;
};

export default function Page() {
    const { setData, post, errors, clearErrors, processing } = useForm<Required<Form>>({
        email: '',
    });

    const handleSubmit = useCallback<FormEventHandler<HTMLFormElement>>(
        (e) => {
            e.preventDefault();
            post(route('password.email'), {});
            clearErrors();
        },
        [post, clearErrors],
    );

    return (
        <>
            <Head title="Forgot Password"></Head>
            <AuthLayout>
                <form
                    onSubmit={handleSubmit}
                    className="flex w-full max-w-sm flex-col gap-6 p-4"
                >
                    <div className="flex flex-col items-start">
                        <h2 className="text-xl font-medium">Forgot Password</h2>
                        <p className="py-1 text-sm">Enter your email to update password</p>
                    </div>

                    <div className="flex flex-col gap-1">
                        <LabelComponent htmlFor="email">Email</LabelComponent>
                        <InputComponent
                            id="email"
                            placeholder="emal@example.com"
                            onChange={(e) => {
                                setData('email', e.currentTarget.value);
                            }}
                        />
                        {errors.email && <ErrorTextComponent children={errors.email} />}
                    </div>

                    <ButtonComponent
                        type="submit"
                        disabled={processing}
                    >
                        Forgot Password
                    </ButtonComponent>
                </form>
            </AuthLayout>
        </>
    );
}
