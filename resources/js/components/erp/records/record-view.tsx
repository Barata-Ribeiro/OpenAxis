import { RecordDetails } from './record-details';
import type { RecordDetailsProps } from './record-details';
import { RecordScreen } from './record-screen';
import type { RecordScreenProps } from './record-screen';

export type RecordViewProps = RecordScreenProps & RecordDetailsProps;

export function RecordView({ title, modal, details, images }: Readonly<RecordViewProps>) {
    return (
        <RecordScreen title={title} modal={modal} description="Record details">
            {() => <RecordDetails details={details} images={images} />}
        </RecordScreen>
    );
}
