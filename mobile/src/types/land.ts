export interface Land {
    id: number;
    name: string;
    area: number; // in acres or hectares
    coordinates: string; // GeoJSON or similar format
    createdAt: string; // ISO date string
    updatedAt: string; // ISO date string
}