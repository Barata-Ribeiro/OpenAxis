import { Badge } from '@/components/ui/badge';
import { normalizeString } from '@/lib/utils';
import { Role } from '@/types/application/role-permission';

export default function RoleBadge({ role }: Readonly<{ role: Pick<Role, 'name'> }>) {
    const badgeVariant = role.name === 'super-admin' ? 'default' : 'secondary';
    const normalizedRoleName = normalizeString(role.name);
    const label = `Role of ${normalizedRoleName}`;

    return (
        <Badge variant={badgeVariant} className="select-none" aria-label={label} title={label}>
            {normalizedRoleName}
        </Badge>
    );
}
