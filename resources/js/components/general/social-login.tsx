import GithubSvg from '../svg/github-svg';
import GoogleSvg from '../svg/google-svg';
import LinkComponent from '../ui/link-component';

export default function SocialLogin() {
    return (
        <div className="flex flex-wrap items-center gap-2">
            <LinkComponent
                href={route('google.login')}
                className="flex flex-1 items-center justify-center gap-2"
            >
                <GoogleSvg />
                Google
            </LinkComponent>
            <LinkComponent
                href={route('github.login')}
                className="flex flex-1 items-center justify-center gap-2"
            >
                <GithubSvg />
                Github
            </LinkComponent>
        </div>
    );
}
