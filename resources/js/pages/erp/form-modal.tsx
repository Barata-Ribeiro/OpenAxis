import Modal from '@/components/custom-ui/modal';
import { RecordForm, type RecordProps } from '@/components/erp/record-form';
import { useState } from 'react';

export default function FormModal(props: Readonly<RecordProps>) {
    const [processing, setProcessing] = useState(false);
    const description =
        props.mode === 'show'
            ? 'Record details'
            : props.mode === 'archive'
              ? 'Confirm this change'
              : 'Complete the fields below';

    return (
        <Modal title={props.title} description={description} processing={processing}>
            {(close) => <RecordForm props={props} close={close} onProcessing={setProcessing} />}
        </Modal>
    );
}
