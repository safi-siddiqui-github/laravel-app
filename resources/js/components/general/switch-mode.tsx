import { useAppearance } from '@/hooks/use-appearance';
import { cn } from '@/lib/utils';
import { Monitor, Moon, Sun } from 'lucide-react';

export default function SwitchMode() {
    const { appearance, updateAppearance } = useAppearance();

    return (
        <div className="flex items-center gap-2 overflow-hidden rounded border">
            <button
                className={cn('cursor-pointer p-1.5', {
                    'bg-black text-white dark:bg-white dark:text-black': appearance == 'system',
                })}
                onClick={() => updateAppearance('system')}
            >
                <Monitor className="size-6" />
            </button>
            <button
                className={cn('cursor-pointer p-1.5', {
                    'bg-black text-white dark:bg-white dark:text-black': appearance == 'light',
                })}
                onClick={() => updateAppearance('light')}
            >
                <Sun className="size-6" />
            </button>
            <button
                className={cn('cursor-pointer p-1.5', {
                    'bg-black text-white dark:bg-white dark:text-black': appearance == 'dark',
                })}
                onClick={() => updateAppearance('dark')}
            >
                <Moon className="size-6" />
            </button>
        </div>
    );
}
