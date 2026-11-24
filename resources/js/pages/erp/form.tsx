import { Card, CardContent } from '@/components/ui/card';
import { RecordForm, recordBreadcrumbs, type RecordProps } from '@/components/erp/record-form';
import { ErpPage } from '@/components/erp/page';
import type { Auth } from '@/types/auth';

export default function FormPage(props: Readonly<RecordProps>) {
    return (
        <ErpPage title={props.title}>
            <Card className="w-full max-w-3xl">
                <CardContent>
                    <RecordForm props={props} />
                </CardContent>
            </Card>
        </ErpPage>
    );
}

FormPage.layout = (props: RecordProps & { auth: Auth }) => ({ breadcrumbs: recordBreadcrumbs(props) });
