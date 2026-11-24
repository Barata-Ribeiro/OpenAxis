import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import {
    CircleCheckBig,
    CircleExclamationPoint,
    CloudUpload,
    FileArchive,
    FileCode,
    FileImage,
    FileQuestionMark,
    FileText,
    FileVideo,
    Trash,
} from 'lucide-react';
import React, { useCallback, useRef, useState } from 'react';

export type FileState = 'queued' | 'uploading' | 'completed' | 'failed';

export interface FileEntry {
    id: string;
    file: File;
    progress: number;
    state: FileState;
    error?: string;
}

export interface FileUploadAreaProps extends Omit<React.HTMLAttributes<HTMLDivElement>, 'onDrop'> {
    title?: string;
    description?: string;
    maxFiles?: number;
    maxSizeMB?: number;
    accept?: string;
    files: FileEntry[];
    onFilesSelect: (files: File[]) => void;
    onFileRemove: (id: string) => void;
}

function formatBytes(bytes: number): string {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function FileIcon({ type, name }: Readonly<{ type: string; name: string }>) {
    const ext = name.split('.').pop()?.toLowerCase() || '';

    if (type.startsWith('image/')) return <FileImage aria-hidden className="size-5" />;
    if (type.startsWith('video/')) return <FileVideo aria-hidden className="size-5" />;
    if (type === 'application/pdf' || ext === 'pdf') return <FileText aria-hidden className="size-5" />;
    if (['zip', 'rar', 'tar', 'gz', '7z'].includes(ext)) return <FileArchive aria-hidden className="size-5" />;
    if (['js', 'ts', 'jsx', 'tsx', 'py', 'json', 'html', 'css'].includes(ext))
        return <FileCode aria-hidden className="size-5" />;
    return <FileQuestionMark aria-hidden className="size-5" />;
}

function CircularProgress({ progress }: Readonly<{ progress: number }>) {
    const radius = 14;
    const circumference = 2 * Math.PI * radius;
    const strokeDashoffset = circumference - (progress / 100) * circumference;

    return (
        <div className="relative inline-flex items-center justify-center">
            <svg role="presentation" className="size-8 -rotate-90 transform">
                <circle
                    className="text-muted"
                    strokeWidth="3"
                    stroke="currentColor"
                    fill="transparent"
                    r={radius}
                    cx="16"
                    cy="16"
                />
                <circle
                    className="text-primary transition-all duration-300 ease-in-out"
                    strokeWidth="3"
                    strokeDasharray={circumference}
                    strokeDashoffset={strokeDashoffset}
                    strokeLinecap="round"
                    stroke="currentColor"
                    fill="transparent"
                    r={radius}
                    cx="16"
                    cy="16"
                />
            </svg>
            <div className="absolute flex items-center justify-center text-xs font-bold text-muted-foreground tabular-nums">
                {Math.round(progress)}
            </div>
        </div>
    );
}

export function FileUploadArea({
    title = 'Media Library',
    description = 'Upload and manage your project files.',
    maxFiles = 5,
    maxSizeMB = 10,
    accept,
    files,
    onFilesSelect,
    onFileRemove,
    className,
    ...props
}: Readonly<FileUploadAreaProps>) {
    const [isDragging, setIsDragging] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleDragOver = useCallback((e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    }, []);

    const handleDragLeave = useCallback((e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    }, []);

    const handleDrop = useCallback(
        (e: React.DragEvent<HTMLDivElement>) => {
            e.preventDefault();
            e.stopPropagation();
            setIsDragging(false);

            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                onFilesSelect(Array.from(e.dataTransfer.files));
            }
        },
        [onFilesSelect],
    );

    const handleFileSelect = useCallback(
        (e: React.ChangeEvent<HTMLInputElement>) => {
            if (e.target.files && e.target.files.length > 0) {
                onFilesSelect(Array.from(e.target.files));
            }
            if (fileInputRef.current) {
                fileInputRef.current.value = '';
            }
        },
        [onFilesSelect],
    );

    return (
        <Card className={cn('mx-auto my-auto w-full max-w-lg rounded-none', className)} {...props}>
            <CardHeader className="rounded-none border-b border-border bg-muted pt-2">
                <CardTitle className="text-md font-medium tracking-tight text-foreground">{title}</CardTitle>
                <CardDescription className="text-xs text-muted-foreground">{description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-5">
                <div
                    className={cn(
                        'relative flex cursor-pointer flex-col items-center justify-between gap-4 rounded-none border-2 border-dashed p-6 transition-all duration-300 outline-none focus-visible:ring-2 focus-visible:ring-ring sm:flex-row',
                        isDragging
                            ? 'border-primary bg-primary/5'
                            : 'border-border bg-muted/10 hover:border-primary/40 hover:bg-muted/30',
                    )}
                    onDragOver={handleDragOver}
                    onDragLeave={handleDragLeave}
                    onDrop={handleDrop}
                    onClick={() => fileInputRef.current?.click()}
                    role="button"
                    tabIndex={0}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            fileInputRef.current?.click();
                        }
                    }}
                >
                    <input
                        type="file"
                        ref={fileInputRef}
                        className="hidden"
                        multiple={maxFiles > 1}
                        accept={accept}
                        onChange={handleFileSelect}
                        aria-hidden
                    />
                    <div className="flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
                        <div
                            className={cn(
                                'rounded p-4 transition-colors duration-300',
                                isDragging
                                    ? 'bg-primary/20 text-primary'
                                    : 'border bg-background text-muted-foreground shadow-xs group-hover:text-primary',
                            )}
                        >
                            <CloudUpload aria-hidden className="size-6" />
                        </div>
                        <div>
                            <p className="text-base font-medium text-foreground">Drop files here or click to browse</p>
                            <p className="mt-1 text-sm text-muted-foreground">
                                Up to {maxFiles} files, {maxSizeMB}MB each
                            </p>
                        </div>
                    </div>
                    <Button type="button">Select Files</Button>
                </div>

                {files.length > 0 && (
                    <div className="space-y-2">
                        {files.map((f) => (
                            <div key={f.id} className="flex items-center gap-4 p-2 text-card-foreground transition-all">
                                <div
                                    className={cn(
                                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-none',
                                        f.state === 'failed'
                                            ? 'bg-destructive/10 text-destructive'
                                            : f.state === 'completed'
                                              ? 'bg-primary/10 text-primary'
                                              : 'bg-muted text-muted-foreground',
                                    )}
                                >
                                    <FileIcon type={f.file.type} name={f.file.name} />
                                </div>

                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-sm font-semibold text-foreground">{f.file.name}</p>
                                    <div className="mt-0.5 flex items-center gap-2 text-xs">
                                        <span className="font-medium text-muted-foreground">
                                            {formatBytes(f.file.size)}
                                        </span>
                                        {f.error && (
                                            <>
                                                <span className="text-muted-foreground">•</span>
                                                <span className="truncate font-medium text-destructive">{f.error}</span>
                                            </>
                                        )}
                                    </div>
                                </div>

                                <div className="flex shrink-0 items-center gap-1">
                                    {f.state === 'uploading' && <CircularProgress progress={f.progress} />}
                                    {f.state === 'completed' && (
                                        <CircleCheckBig aria-hidden className="size-6 text-primary" />
                                    )}
                                    {f.state === 'failed' && (
                                        <CircleExclamationPoint aria-hidden className="size-6 text-destructive" />
                                    )}

                                    <div className="ml-2 h-6 w-px bg-border" />

                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            onFileRemove(f.id);
                                        }}
                                        className="text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                        aria-label={`Remove ${f.file.name}`}
                                    >
                                        <Trash aria-hidden className="size-4" />
                                    </Button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
