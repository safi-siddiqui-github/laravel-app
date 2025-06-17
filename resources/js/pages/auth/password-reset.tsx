import AuthLayout from '@/components/layout/auth-layout';
import ButtonComponent from '@/components/ui/button-component';
import ErrorTextComponent from '@/components/ui/error-text-component';
import InputComponent from '@/components/ui/input-component';
import LabelComponent from '@/components/ui/label-component';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useCallback } from 'react';

type Form = {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export default function Page() {
    const { email, token } = usePage<Required<{ token: string; email: string }>>().props;

    const { setData, data, reset, post, errors, clearErrors, processing } = useForm<Required<Form>>({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = useCallback<FormEventHandler<HTMLFormElement>>(
        (e) => {
            e.preventDefault();
            post(route('password.resetForm'), {});
            reset('password');
            reset('password_confirmation');
            clearErrors();
        },
        [post, reset, clearErrors],
    );

    return (
        <>
            <Head title="Update Password"></Head>
            <AuthLayout>
                <form
                    onSubmit={handleSubmit}
                    className="flex w-full max-w-sm flex-col gap-6 p-4"
                >
                    <div className="flex flex-col items-start">
                        <h2 className="text-xl font-medium">Update Password</h2>
                        <p className="py-1 text-sm">Make a new strong secure password</p>
                    </div>

                    <div className="flex flex-col gap-1">
                        <LabelComponent htmlFor="email">Email</LabelComponent>
                        <InputComponent
                            defaultValue={data.email}
                            disabled={true}
                        />
                        {errors.email && <ErrorTextComponent children={errors.email} />}
                    </div>

                    <div className="flex flex-col gap-1">
                        <LabelComponent htmlFor="password">Password</LabelComponent>
                        <InputComponent
                            id="password"
                            placeholder="**********"
                            onChange={(e) => {
                                setData('password', e.currentTarget.value);
                            }}
                        />
                        {errors.password && <ErrorTextComponent children={errors.password} />}
                    </div>

                    <div className="flex flex-col gap-1">
                        <LabelComponent htmlFor="password_confirmation">Password Confirmation</LabelComponent>
                        <InputComponent
                            id="password_confirmation"
                            placeholder="**********"
                            onChange={(e) => {
                                setData('password_confirmation', e.currentTarget.value);
                            }}
                        />
                        {errors.password_confirmation && <ErrorTextComponent children={errors.password_confirmation} />}
                    </div>

                    <ButtonComponent
                        type="submit"
                        disabled={processing}
                    >
                        Update Password
                    </ButtonComponent>
                </form>
            </AuthLayout>
        </>
    );
}
