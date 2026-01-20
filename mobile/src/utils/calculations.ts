// Utility functions for calculations related to land measurements

/**
 * Calculate the area of a polygon given its vertices.
 * @param vertices - Array of points representing the vertices of the polygon.
 * @returns Area of the polygon in square meters.
 */
export const calculatePolygonArea = (vertices: { lat: number; lng: number }[]): number => {
    let area = 0;
    const n = vertices.length;

    for (let i = 0; i < n; i++) {
        const j = (i + 1) % n;
        area += vertices[i].lng * vertices[j].lat;
        area -= vertices[j].lng * vertices[i].lat;
    }

    area = Math.abs(area) / 2;
    return area; // Area in square meters
};

/**
 * Convert area from square meters to acres.
 * @param areaInSquareMeters - Area in square meters.
 * @returns Area in acres.
 */
export const convertSquareMetersToAcres = (areaInSquareMeters: number): number => {
    return areaInSquareMeters * 0.000247105; // 1 square meter = 0.000247105 acres
};

/**
 * Convert area from square meters to hectares.
 * @param areaInSquareMeters - Area in square meters.
 * @returns Area in hectares.
 */
export const convertSquareMetersToHectares = (areaInSquareMeters: number): number => {
    return areaInSquareMeters * 0.0001; // 1 square meter = 0.0001 hectares
};