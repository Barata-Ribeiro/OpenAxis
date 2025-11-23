import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { formatBytes, useFileUpload, type FileWithPreview } from '@/hooks/use-file-upload';
import { cn } from '@/lib/utils';
import { TriangleAlert, User, X } from 'lucide-react';
import { Activity } from 'react';

interface AvatarUploadProps {
    maxSize?: number;
    className?: string;
    onFileChange?: (file: FileWithPreview | null) => void;
    defaultAvatar?: string;
}

export default function AvatarUpload({
    maxSize = 2 * 1024 * 1024, // 2MB
    className,
    onFileChange,
    defaultAvatar,
}: Readonly<AvatarUploadProps>) {
    const [
        { files, isDragging, errors },
        { removeFile, handleDragEnter, handleDragLeave, handleDragOver, handleDrop, openFileDialog, getInputProps },
    ] = useFileUpload({
        maxFiles: 1,
        maxSize,
        accept: 'image/*',
        multiple: false,
        onFilesChange: (files) => onFileChange?.(files[0] || null),
    });

    const currentFile = files[0];
    const previewUrl = currentFile?.preview || defaultAvatar;

    const handleRemove = () => {
        if (currentFile) {
            removeFile(currentFile.id);
        }
    };

    const avatarDropZoneClass = cn(
        'group/avatar relative size-24 cursor-pointer overflow-hidden rounded-full border border-dashed transition-colors',
        isDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-muted-foreground/20',
        previewUrl && 'border-solid',
    );

    return (
        <div className={cn('flex flex-col items-center gap-4', className)}>
            {/* Avatar Preview */}
            <div className="relative">
                <div
                    role="button"
                    tabIndex={0}
                    aria-label="Upload avatar"
                    className={avatarDropZoneClass}
                    onDragEnter={handleDragEnter}
                    onDragLeave={handleDragLeave}
                    onDragOver={handleDragOver}
                    onDrop={handleDrop}
                    onClick={openFileDialog}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                            e.preventDefault();
                            openFileDialog();
                        }
                    }}
                >
                    <input {...getInputProps()} className="sr-only" />

                    {previewUrl ? (
                        <img src={previewUrl} alt="Avatar" className="h-full w-full object-cover" />
                    ) : (
                        <div className="flex h-full w-full items-center justify-center">
                            <User className="size-6 text-muted-foreground" />
                        </div>
                    )}
                </div>

                {/* Remove Button - only show when file is uploaded */}
                <Activity mode={currentFile ? 'visible' : 'hidden'}>
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        onClick={handleRemove}
                        className="absolute end-0 top-0 size-6 rounded-full"
                        aria-label="Remove avatar"
                    >
                        <X aria-hidden className="size-3.5" />
                    </Button>
                </Activity>
            </div>

            {/* Upload Instructions */}
            <div className="space-y-0.5 text-center">
                <p className="text-sm font-medium">{currentFile ? 'Avatar uploaded' : 'Upload avatar'}</p>
                <p className="text-xs text-muted-foreground">PNG, JPG up to {formatBytes(maxSize)}</p>
            </div>

            {/* Error Messages */}
            {errors.length > 0 && (
                <Alert variant="destructive" className="mt-5">
                    <TriangleAlert aria-hidden />
                    <AlertTitle>File upload error(s)</AlertTitle>
                    <AlertDescription>
                        <ul className="list-inside list-disc text-sm">
                            {errors.map((error) => (
                                <li key={error} className="last:mb-0">
                                    {error}
                                </li>
                            ))}
                        </ul>
                    </AlertDescription>
                </Alert>
            )}
        </div>
    );
}
