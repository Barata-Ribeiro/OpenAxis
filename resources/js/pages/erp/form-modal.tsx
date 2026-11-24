import Modal from '@/components/custom-ui/modal';
import { RecordForm, useRecordForm, type RecordProps } from '@/components/erp/record-form';

export default function FormModal(props: Readonly<RecordProps>) {
    const model = useRecordForm(props);
    const description =
        props.mode === 'show'
            ? 'Record details'
            : props.mode === 'archive'
              ? 'Confirm this change'
              : 'Complete the fields below';

    return (
        <Modal title={props.title} description={description} processing={model.form.processing}>
            {(close) => <RecordForm props={props} model={model} close={close} />}
        </Modal>
    );
}
