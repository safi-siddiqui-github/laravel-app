import SocialLogin from '@/components/general/social-login';
import AuthLayout from '@/components/layout/auth-layout';
import ButtonComponent from '@/components/ui/button-component';
import ErrorTextComponent from '@/components/ui/error-text-component';
import InputComponent from '@/components/ui/input-component';
import LabelComponent from '@/components/ui/label-component';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler, useCallback } from 'react';

type Form = {
    email: string;
    password: string;
    remember: boolean;
};

export default function Page() {
    const { setData, data, reset, post, errors, clearErrors, processing } = useForm<Required<Form>>({
        email: '',
        password: '',
        remember: true,
    });

    const handleSubmit = useCallback<FormEventHandler<HTMLFormElement>>(
        (e) => {
            e.preventDefault();
            post(route('login.form'), {});
            reset('password');
            clearErrors();
        },
        [post, reset, clearErrors],
    );

    return (
        <>
            <Head title="Login"></Head>
            <AuthLayout>
                <form
                    onSubmit={handleSubmit}
                    className="flex w-full max-w-sm flex-col gap-6 p-4"
                >
                    <div className="flex flex-col items-start">
                        <h2 className="text-xl font-medium">Login</h2>
                        <Link
                            href={route('register.page')}
                            className="py-1 text-sm"
                        >
                            Create your new Account?
                        </Link>
                    </div>

                    <SocialLogin />

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

                    <div className="flex flex-col gap-1">
                        <div className="flex flex-wrap justify-between">
                            <LabelComponent htmlFor="password">Password</LabelComponent>
                            <Link
                                href={route('password.request')}
                                className="text-sm"
                            >
                                Forgot Password?
                            </Link>
                        </div>

                        <InputComponent
                            id="password"
                            placeholder="**********"
                            onChange={(e) => {
                                setData('password', e.currentTarget.value);
                            }}
                        />
                        {errors.password && <ErrorTextComponent children={errors.password} />}
                    </div>

                    <div className="flex flex-row items-center gap-2">
                        <InputComponent
                            id="remember"
                            type="checkbox"
                            className="size-4"
                            defaultChecked={data.remember}
                            onChange={(e) => {
                                setData('remember', e.currentTarget.checked);
                            }}
                        />
                        <LabelComponent htmlFor="remember">Remember me</LabelComponent>
                    </div>

                    <ButtonComponent
                        type="submit"
                        disabled={processing}
                    >
                        Login
                    </ButtonComponent>
                </form>
            </AuthLayout>
        </>
    );
}
