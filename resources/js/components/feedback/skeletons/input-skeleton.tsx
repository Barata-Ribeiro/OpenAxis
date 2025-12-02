import { Field } from '@/components/ui/field';
import { Skeleton } from '@/components/ui/skeleton';

export default function InputSkeleton() {
    return (
        <Field aria-label="Loading in progress..." title="Loading in progress..." role="status">
            <Skeleton aria-hidden className="h-6 w-full" />
            <Skeleton aria-hidden className="h-10 w-full" />
        </Field>
    );
}
