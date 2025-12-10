export interface Media {
    id: number;
    model_type: string;
    model_id: number;
    uuid: string;
    collection_name: string;
    name: string;
    file_name: string;
    mime_type: string;
    disk: string;
    conversions_disk: string;
    size: number;
    manipulations: unknown[]; // Adjust type as needed
    custom_properties: unknown[]; // Adjust type as needed
    generated_conversions: unknown[]; // Adjust type as needed
    responsive_images: ResponsiveImages;
    order_column: number;
    created_at: string;
    updated_at: string;
    original_url: string;
    preview_url: string;
}

export interface ResponsiveImages {
    media_library_original: MediaLibraryOriginal;
}

export interface MediaLibraryOriginal {
    urls: string[];
    base64svg: string;
}
