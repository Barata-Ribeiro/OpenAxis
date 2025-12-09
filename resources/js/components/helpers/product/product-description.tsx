import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText } from 'lucide-react';

export default function ProductDescription({ description }: Readonly<{ description: string }>) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2 text-base">
                    <FileText aria-hidden size={16} />
                    Description
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p className="text-sm leading-relaxed whitespace-pre-wrap text-muted-foreground">{description}</p>
            </CardContent>
        </Card>
    );
}
