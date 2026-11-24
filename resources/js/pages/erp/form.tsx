import { Card, CardContent } from '@/components/ui/card';
import { RecordBack, RecordForm, useRecordForm, type RecordProps } from '@/components/erp/record-form';
import { ErpPage } from '@/components/erp/page';

export default function FormPage(props: Readonly<RecordProps>) {
    const model = useRecordForm(props);

    return (
        <ErpPage title={props.title} actions={<RecordBack kind={props.kind} />}>
            <Card className="w-full max-w-3xl">
                <CardContent>
                    <RecordForm props={props} model={model} />
                </CardContent>
            </Card>
        </ErpPage>
    );
}
